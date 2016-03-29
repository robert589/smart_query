<?php
namespace frontend\controllers;

use Yii;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\base\ErrorException;
use yii\base\InvalidParamException;
use yii\data\ArrayDataProvider;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\httpclient\Client;
use yii\helpers\Json;
use yii\web\JsonResponseFormatter;
use common\models\QueryForm;
use yii\web\JsonParser;
use common\components\CustomDataProvider;
/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }
    public function actionIndex()
    {   if (isset($_POST['category'])) {
            $category = $_POST['category'];
        } else if (isset($_GET['category'])) {
            $category = $_GET['category'];
        } else {
            $category = '';
        }


        if (isset($_POST['source'])) {
            $source = $_POST['source'];
        } else if (isset($_GET['source'])) {
            $source = $_GET['source'];
        } else {
            $source = '';
        }


        if (isset($_POST['query'])) {
            $query = $_POST['query'];
        }else if (isset($_GET['source'])) {
            $query = $_GET['query'];
        } else {
            $query = '';
        }


        if (isset($_POST['sort_by'])) {
            $sort_by = $_POST['sort_by'];
        } else if (isset($_GET['sort_by'])) {
            $sort_by = $_GET['sort_by'];
        }else {
            $sort_by = '';
        }

        $url = $this->getUrl($query, $source, $sort_by, $category);
        $data_provider = new CustomDataProvider([
                'url' => $url,
                'pagination' => ['pageSize' => 10,
                    'params' => [
                        'per-page' => isset($_GET['per-page']) ? $_GET['per-page'] : 10,
                        'page' => isset($_GET['page']) ? $_GET['page'] : 1,
                        'source' => $source,
                        'sort_by' => $sort_by,
                        'query' => $query,
                        'category' => $category
                    ]
                ]
            ]
        );

        return $this->render('index', ['data_provider' => $data_provider,
            'source' => $source,
            'category' => $category,
            'query' => $query,
            'sort_by' => $sort_by]);
    }

    public function actionSpellList($q = null){
        $out = [];
        if($q == null) {
            $q = '*';
        }

        $q = str_replace(' ', '%20', $q);

       $url = "http://solr.kenrick95.xyz/solr/cz4034/spell?q=message%3A'$q'&rows=0&wt=json&indent=true";

       $client = new Client();

       $results = $client->createRequest()
           ->setMethod('post')
           ->setUrl($url)
           ->send();

        if($results->isOk){
            $results = $results->getData();
            if($results['spellcheck']['correctlySpelled'] == false){
                $results = $results['spellcheck']['suggestions'][1]['suggestion'];

                foreach($results as $result){
                    $out[] = ['value' => $result['word'] ];
                }
            }
        }


        echo Json::encode($out);

    }

    public  function actionCrawl(){
        Yii::$app->end('hello');
        if(isset($_POST['news'])) {
            $news = $_POST["news"];
            $tail = "&access_token=" . $_POST["accesscode"];
            $filename = $news . ".txt";
            $max = 50;
            $header = "https://api.facebook.com/method/fql.query?format=json-strings&query=";
            //$tail ="&access_token=CAACEdEose0cBAKEkJn36B6YwHjigwH4F1gRrSRgAeZBY11LIZBFx8s2tQkI15ZACVHKZCPG0ghXXkgRgQ5Ijc1evYZAa6gK92Q9aSvZA4LE2oZChNlwU3eMmbY0naQZCrB7uUUo39AOMLLZC6qsnOYiYz0f0sNL3UO1tsW2WTLkKUIiD2CKm9MA0wZA6zCgENJfhEZBYON8BwowZCAZDZD";

            //set timestamp
            $time = fopen($filename, r);
            $timestamp = fgets($time);
            //echo "<br><br>$timestamp";
            fclose($time);
            $name = $news . ".json";
            $file = fopen($name, "r");
            $text = fread($file, filesize($name));
            fclose($file);
            //Determine Source id
            if ($news == "straits-times") {
                $source = 129011692114;
            } else if ($news == "cnn") {
                $source = 5550296508;
            } else if ($news == "bbc") {
                $source = 228735667216;
            } else if ($news == "reuters") {
                $source = 114050161948682;
            } else {
                $source = 10513336322;
            }

            $try = $header . "SELECT%20post_id,created_time,attachment,message,like_info%20FROM%20stream%20WHERE%20source_id=" . $source . "%20%20and%20created_time>" . $timestamp . "%20" . $tail;
            echo "$try<br><br>";
            $json = file_get_contents($try);
            $json_output = json_decode($json);

            $max = SIZEOF($json_output);

            $dunno = get_object_vars($json_output[0]);
            $timestamp = $dunno[created_time];

            if ($timestamp != "") {
                $time = fopen($filename, w);
                fwrite($time, $timestamp);
                fclose($time);
            }

            $revised = json_encode($json_output);
            /*
                $file = fopen($name,"w");
                if (strlen($text)>1)
                {
                $revised = substr($revised,0,strlen($revised)-2);
                }
                if (strlen($revised)>1)
                {
                $text=substr($text,1);
                }
                $finalwrite = $revised.",".$text;
                echo fwrite($file,$finalwrite);
            */
            $url = 'http://solr.kenrick95.xyz:82/classify';
            $data = array('text' => $revised, 'filename' => $news);
            // use key 'http' even if you send the request to https://...
            $options = array(
                'http' => array(
                    'method' => 'POST',
                    'content' => http_build_query($data)
                )
            );
            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            if ($result === FALSE) { /* Handle error */
            }
            var_dump($result);
            echo '<script type="text/javascript">alert("' . $max . "entries has been added to " . $news . " database" . '");
                    window.location.href=\'crawl.html\';

                    </script>';
            //print_r($json_output);

            error_reporting(E_ALL);
        }
        return $this->redirect(Yii::$app->request->baseUrl);
    }


    function jsonp_decode($jsonp, $assoc = false) { // PHP 5.3 adds depth as third parameter to json_decode
        if($jsonp[0] !== '[' && $jsonp[0] !== '{') { // we have JSONP
            $jsonp = substr($jsonp, strpos($jsonp, '('));
        }
        return json_decode(trim($jsonp,'();'), $assoc);
    }

    function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    function xml2array ( $xmlObject, $out = array () )
    {
        foreach ( (array) $xmlObject as $index => $node )
            $out[$index] = ( is_object ( $node ) ) ? xml2array ( $node ) : $node;

        return $out;
    }


    private function getUrl($query, $source, $sort_by, $category){

        $url = "http://solr.kenrick95.xyz/solr/cz4034/select?&wt=json&indent=true";

        //query
        if($query == '' || $query == null){
            $url .= '&q=message%3A' . '*' . '';
        }
        else{
            if($queries = explode(' ', $query)){
                foreach($queries as $item){
                    $url .= '&q=message%3A"' . $item . '"';

                }
            }
        }
        //source
        if($source != '' || $source != null  ){
            if($source == 'Straits Times'){
                $source = 'Straits%20Times';
            }
            if($source != 'All'){
                $url .= "&fq=source%3A\"" . $source . "\"";

            }
        }

        //sort_by
        if($sort_by == 'Popularity'){
            $url .= "&sort=like_count+desc";

        }
        else if($sort_by == 'Latest'){
            $url .= "&sort=created_time+desc";
        }

        //category
        if($category != null){
            if($category != 'All'){
                $url .= '&fq=category%3A' . $category;

            }

        }
        return $url;

    }
}
