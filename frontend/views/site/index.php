<?php
    use kartik\tabs\TabsX;
    use kartik\helpers\Html;
use yii\bootstrap\Modal;
    /** @var $data_provider \common\components\CustomDataProvider */
    /** @var $category string */
    /** @var $source string */
    /** @var $sort_by string */
    /** @var $query string */

$this->title = 'Smart Query';

\yii\bootstrap\BootstrapPluginAsset::register($this);

$items = [
        [
            'label' => 'All'
        ],
        [
            'label' => 'BBC'
        ],
        [
            'label' => 'Straits Times'
        ],
        [
            'label' => 'Guardian'
        ],
        [
            'label' => 'CNN'
        ],
        [
            'label' => 'Reuters'
        ]
    ];

    $category_items = [
           'All' => 'All',
           'Politics' => 'Politics',
           'Social' => 'Social',
           'Technology' => 'Technology',
           'Sports' => 'Sports',
   ];

$sort_by_items = [
        'Relevance' => 'Relevance',

        'Popularity' => 'Popularity',
        'Latest' => 'Latest'

];
?>
<?php
Modal::begin([
    'id' => 'crawlModal'
]);
echo $this->render('crawl');
Modal::end();
?>

    <div id = "left-sidebar">
        <div align="center">
            <h5>CATEGORY</h5>
        </div>

        <?= Html::radioButtonGroup('category_sidenav', 'All', $category_items, ['separator' => '<br>',]) ?>

        <br>
        <br>

        <div align="center">
            <h5>SORT BY</h5>
        </div>
        <?= Html::radioButtonGroup('sort_by_sidenav', 'Relevance', $sort_by_items, ['separator' => '<br>',]) ?>

        <br><br>
        <br><br>
        <div align="center">
            <?= Html::button('Update Data', ['class' => 'btn btn-lg btn-primary' , 'id' => 'update_data', 'align' => 'center']) ?>

        </div>

    </div>
    <div class="col-md-9 col-xs-9 col-md-offset-3">
        <div class='col-md-12 marginer'>
            <?= TabsX::widget([
                'id' => 'source_tabsx',
                'items'=>$items,
                'position'=>TabsX::POS_ABOVE,
                'encodeLabels'=>false,
            ]) ?>
        </div>

        <?= $this->render('_news_pjax', [
                                        'data_provider' => $data_provider,
                                        'category' => $category,
                                        'source' => $source,
                                        'query' => $query,
                                        'sort_by' => $sort_by]) ?>

    </div>
