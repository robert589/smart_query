<?php
namespace frontend\controllers;

use Yii;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\base\InvalidParamException;
use yii\data\ArrayDataProvider;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\QueryForm;

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

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {

        if(isset($_POST['data'])){

            $data = json_decode($_POST['data'], true);
            $processing_to_provider = array();
            $query_form = new QueryForm();

            $query_form->category = $_POST['QueryForm']['category'];
            if(isset($_POST['query'])){
                Yii::$app->view->params['query'] = $_POST['query'];
            }
            else{
                Yii::$app->view->params['query'] = "";
            }

            if(isset($_POST['category'])){
                $category = $_POST['category'];
            }
            else{
                $category = '';
            }

            if(isset($_POST['sort_by'])){
                $sort_by = $_POST['sort_by'];
            }
            else{
                $sort_by = "";
            }

            if(isset($_POST['spell_checker'])){
                $spell_check = $_POST['spell_checker'];
            }
            else{
                $spell_check = "";
            }

            foreach($data as $datum){
                //Yii::$app->end(print_r($datum));
                $temp_data['link_source'] = $datum['attachment_media_href'];
                $temp_data['media'] = $datum['attachment_media_fullsize_src'];
                $temp_data['message'] = $datum['message'];
                $temp_data['source'] = $datum['source'];
                $temp_data['category'] = $datum['category'];
                $temp_data['created_time'] = $datum['created_time'];
                $processing_to_provider[] = $temp_data;
            }

            $data_provider = new ArrayDataProvider([
                'allModels' => $processing_to_provider,
                'pagination' => [
                    'pageSize' => sizeof($processing_to_provider),
                ],
            ]);
            return $this->render('index', ['has_data' => true,
                'data_provider' => $data_provider,
                'query_form' => $query_form,
                'sort_by' => $sort_by,
                'category' => $category
                ,'spell_check' => $spell_check]);
        }
        else{
            $sort_by = "";
            $spell_check = "";
            $query_form = new QueryForm();
            return $this->render('index', ['query_form' => $query_form,
                'sort_by' => $sort_by,
                'category' => "",
                'spell_check' => $spell_check]);

        }
    }

    public function actionProcess(){
        $data = json_decode($_POST['data'], true);
        $processing_to_provider = array();
        $query_form = new QueryForm();

        foreach($data as $datum){

            $temp_data['message'] = $datum['message'];

            $processing_to_provider[] = $temp_data;
        }

        $data_provider = new ArrayDataProvider([
            'allModels' => $processing_to_provider,
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);
        return $this->render('index', ['has_data' => true, 'data_provider' => $data_provider, 'query_form' => $query_form]);
    }
    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
}
