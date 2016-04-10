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


        if (isset($_REQUEST['sort_by'])) {
            $sort_by = $_REQUEST['sort_by'];
        }else {
            $sort_by = '';
        }
        if(!Yii::$app->request->isPjax){
            if($query == null && !isset($_REQUEST['sort_by'])){

                $sort_by = 'Latest';
            }
        }
        else{
            if($query != null && !isset($_REQUEST['sort_by'])){
                $sort_by = '';
            }
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



        if($query != ''){
            $sort_by  = 'Relevance';
            $suggestion = $this->suggestion($query);
        }
        else{
            $suggestion = '';
        }

        return $this->render('index', ['data_provider' => $data_provider,
            'source' => $source,
            'category' => $category,
            'query' => $query,
            'suggestion' => $suggestion,
            'sort_by' => $sort_by]);
    }

    public function suggestion($q){
        $url = "http://solr.kenrick95.xyz/solr/cz4034/spell?q=message%3A%22$q%22&rows=0&wt=json&indent=true";

        $client = new Client();

        $results = $client->createRequest()
            ->setMethod('post')
            ->setUrl($url)
            ->send();

        if($results->isOk){
            $results = $results->getData();
            if($results['spellcheck']['correctlySpelled'] == false){
                $result =  $results['spellcheck']['suggestions'][1]['suggestion'][0]['word'];
                return $result;
            }
        }


    }

    public function actionSpellList($q = null){
        $out = [];
        if($q == null) {
            $q = '*';
        }

        $q = str_replace(' ', '%20', $q);

       $url = "http://solr.kenrick95.xyz/solr/cz4034/spell?q=message%3A%22$q%22&rows=0&wt=json&indent=true";

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
        if(isset($_POST['news']) && isset($_POST['accesscode'])) {
            //Yii::$app->end($_POST['news']);
            $news = $_POST["news"];
            $tail = "&access_token=" . $_POST["accesscode"];
            $filename = Yii::getAlias('@text') . '/'.  $news . ".txt";
            $max = 50;
            $header = "https://api.facebook.com/method/fql.query?format=json-strings&query=";
            //$tail ="&access_token=CAACEdEose0cBAKEkJn36B6YwHjigwH4F1gRrSRgAeZBY11LIZBFx8s2tQkI15ZACVHKZCPG0ghXXkgRgQ5Ijc1evYZAa6gK92Q9aSvZA4LE2oZChNlwU3eMmbY0naQZCrB7uUUo39AOMLLZC6qsnOYiYz0f0sNL3UO1tsW2WTLkKUIiD2CKm9MA0wZA6zCgENJfhEZBYON8BwowZCAZDZD";

            //set timestamp
            $time = fopen($filename, 'r');

            $timestamp = fgets($time);
            //echo "<br><br>$timestamp";
            fclose($time);
            //Determine Source id
           if ($news == "straits-times") {
                $source =   129011692114;
            } else if ($news == "cnn") {
                $source = 5550296508;
            } else if ($news == "bbc") {
                $source = 228735667216;
            } else if ($news == "reuters") {
                $source = '%27114050161948682%27';
            } else {
                $source = 10513336322;
            }

            $try = $header . "SELECT%20post_id,created_time,attachment,message,like_info%20FROM%20stream%20WHERE%20source_id=" . $source .
                "%20%20and%20created_time>" . $timestamp . "%20" . $tail;


            $json = file_get_contents($try);
            $json_output = json_decode($json, false);

            $max = SIZEOF($json_output);
            $dunno = get_object_vars($json_output[0]);
            $timestamp = $dunno['created_time'];


            $revised = json_encode($json_output);
           $url = 'http://10.27.29.94:8080/web/classify';
            $data = array('text' => $revised, 'filename' => $news);

            $file = fopen(Yii::getAlias('@text'). '/output.txt', 'w');
            fwrite($file, $json);
            fclose($file);
            $options = array(
                'http' => array(
                    'method' => 'POST',
                    'header' => 'Content-type: application/x-www-form-urlencoded',
                    'content' => http_build_query($data)
                )
            );
            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            if ($result === FALSE) { /* Handle error */
            }
            var_dump($result);
            echo '<script type="text/javascript">alert("' . $max . "entries has been added to " . $news . " database" . '");

                    </script>';

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
            if(strpos($query, '"') === false){

                $queries = explode(' ', $query);

                //case more than one space
                $url .= '&q=';
                $first = 1;
                foreach($queries as $item){
                    if($first == 1){
                        $url .= 'message%3A' . urlencode($item) . '';
                        $first = 0;
                    }
                    else{
                        $url .= '%20OR%20message%3A' . urlencode($item) . '';

                    }
                    $url .= '%20OR%20attachment_name%3A' . urlencode($item) . '';
                }
            }
            else{
                $url .= '&q=';
                $url .= 'message%3A' . urlencode($query) . '';
                $url .= '%20OR%20attachment_name%3A' . urlencode($query) . '';
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
        } else if ($sort_by == 'Relevance' && $query == '') {
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
