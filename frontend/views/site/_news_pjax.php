
<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Pjax;
use kop\y2sp\ScrollPager;
use yii\widgets\ListView;
/** @var $data_provider \common\components\CustomDataProvider */
/** @var $category string */
/** @var $source string */
/** @var $sort_by string */
/** @var $query string */

?>
<?php $form = ActiveForm::begin(['id' => 'form_up']) ?>

    <?= Html::hiddenInput('category', $category, ['id' =>  'category'] ) ?>

    <?= Html::hiddenInput('query', $query, ['id' => 'query']) ?>

    <?= Html::hiddenInput('sort_by', $sort_by, ['id' => 'sort_by']) ?>


    <?= Html::hiddenInput('source', $source, ['id' =>  'source'] ) ?>

<div id="news-div">

    <?= ListView::widget([
        'id' => 'news_list',
        'summary' => false,
        'dataProvider' => $data_provider,
        'itemOptions' => ['class' => 'item'],
        'layout' => "{summary}\n{items}\n{pager}",
        'pager' => [
            'class' => ScrollPager::class,
            'enabledExtensions' => [
                ScrollPager::EXTENSION_TRIGGER,
                ScrollPager::EXTENSION_SPINNER,
                ScrollPager::EXTENSION_NONE_LEFT,
                ScrollPager::EXTENSION_PAGING,
            ],
            'triggerOffset' => 100
        ],
        'itemView' => function ($model, $key, $index, $widget) {
            return $this->render('_list_query',['model' => $model]);
        }
    ])
    ?>
</div>

<?php ActiveForm::end() ?>
