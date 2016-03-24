<?php
    use kartik\select2\Select2;
    use kartik\widgets\ActiveForm;
    use yii\widgets\ListView;
    use yii\helpers\Html;/* @var $this yii\web\View */
    use kartik\sidenav\SideNav;
    $data = ['All', 'Strait times', 'BBC', 'Reuters' , 'Guardians', 'CNN'];
    $this->title = 'Smart Query';

?>

<div class="site-index">
    <?php $form = ActiveForm::begin(['action' => ['site/index'], 'method' => 'post', 'id' => 'query_form']) ?>

    <div class="row">
        <div class="col-md-7">
            <?= $form->field($query_form, 'category')->radioButtonGroup($data,[
                'class' => 'btn-group-md',
                'itemOptions' => ['labelOptions' => ['class' => 'btn btn-warning']]
            ])->label(false) ?>
        </div>

        <div class="col-md-2">
            <?= Select2::widget([
                'name' => 'sort_by',
                'id' => 'sort_by',
                'value' => $sort_by,
                'hideSearch' => true,
                'data' => ['Popularity' => 'Popularity', 'Latest' => 'Latest'],
                'options' => ['placeholder' => 'Sorted By'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]) ?>
        </div>
        <?= Html::hiddenInput('category', $category,['id' => 'category']) ?>

        <?= Html::hiddenInput('has_data', isset($has_data),['id' => 'has_data']) ?>
        <?= Html::hiddenInput('data', null, ['id' => 'hi_data']) ?>
        <?= Html::hiddenInput('query', null, ['id' => 'query']) ?>
        <?= Html::hiddenInput('spell_checker', null, ['id' => 'spell_checker'])?>
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="row">
                    <div class="col-md-8">
                        <?php if(isset($spell_check)  && $spell_check != ""){?>
                            <?= 'Did you mean ' . $spell_check  . '?' ?>
                        <?php } ?>
                    </div>
            </div>

            <hr>


            <br>

            <?php if(isset($has_data)){ ?>
                <div class="row" id="list_data">
                    <?= ListView::widget([
                        'id' => 'threadList',
                        'dataProvider' => $data_provider,
                        'pager' => ['class' => \kop\y2sp\ScrollPager::className()],
                        'summary' => false,
                        'itemOptions' => ['class' => 'item'],
                        'layout' => "{summary}\n{items}\n{pager}",
                        'itemView' => function ($model, $key, $index, $widget) {
                            return $this->render('_list_query',['model' => $model]);
                        }
                    ])
                    ?>
                </div>

            <?php } ?>
            <?php $form = ActiveForm::end() ?>

        </div>
    </div>
</div>
