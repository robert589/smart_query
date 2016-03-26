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
    {

        if (isset($_POST['category'])) {
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
       $url = "http://solr.kenrick95.xyz/solr/cz4034/spell?q=message%3A'$q'&rows=0&wt=json&indent=true";

       $client = new Client();

       $results = $client->createRequest()
           ->setMethod('post')
           ->setUrl($url)
           ->send();

        if($results->isOk){

            $results = $results->getData();
            if($results['spellcheck']['correctlySpelled'] == false){
                $results = $results['spellcheck']['suggestions']['1']['suggestion'];

                foreach($results as $result){
                    $out[] = ['value' => $result['word'] ];
                }

            }

        }
        else{

        }


        echo Json::encode($out);

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
            $url .= '&q=message%3A"' . $query . '"';
        }

        //source
        if($source != '' || $source != null){
            $url .= '&fq=source%3A"' . $source . '"';
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
            $url .= '&fq=category%3A' . $category;

        }

        return $url;

    }
}
