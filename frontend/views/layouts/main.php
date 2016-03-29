<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;
use kartik\select2\Select2;
use kartik\widgets\SideNav;
use kartik\widgets\Typeahead;
use yii\helpers\Url;
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php
if(!isset(Yii::$app->view->params['query'])){
    Yii::$app->view->params['query'] = "";
}
$this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'Smart Query',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top mynavbar',
        ],
    ]) ?>

    <div class="collapse navbar-collapse col-md-offset-3">
      <form class="navbar-form navbar-left mynav" role="search">
        <div class="form-group">
          <?=
                 Typeahead::widget([
                    'name' => 'search_box',
                     'id' => 'search_box',
                    'options' => ['placeholder' => 'Search here ...'],
                    'scrollable' => true,
                    'pluginOptions' => ['highlight'=>true],
                    'dataset' => [[

                        'datumTokenizer' => "Bloodhound.tokenizers.obj.whitespace('value')",
                        'display' => 'value',
                        'remote' =>
                            [
                                'url' => Url::to(['site/spell-list?q=%QUERY']),
                                'wildcard' => '%QUERY'
                            ]
                        ,
                        'limit' => 10,

                    ]
                    ]
                ])
                 ?>
        </div>
        <?= Html::button('Search' , ['class' => 'btn btn-primary', 'style' => 'margin-top:5px', 'id' => 'query_button']) ?>
      </form>
    </div>
<?php
    NavBar::end();
    ?>

    <div class="container marginer">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>

        <div class="col-md-12 col-xs-12">
            <?= $content ?>
        </div>
        <div id="loading-bar" style="display: none">
            <div style='position:absolute;z-index:0;left:0;top:0;width:100%;height:100%;background-color:white;opacity: 0.4;'>
                Loading
            </div>
        </div>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>
<?php
$this->registerJsFile(Yii::$app->request->baseUrl . '/frontend/web/js/jquery.js');
$this->registerJsFile(Yii::$app->request->baseUrl . '/frontend/web/js/script.js') ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
