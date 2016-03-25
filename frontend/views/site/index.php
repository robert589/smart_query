<?php
    use kartik\sidenav\SideNav;
    use kartik\tabs\TabsX;
    /** @var $data_provider \common\components\CustomDataProvider */
    /** @var $category string */
    /** @var $source string */
    /** @var $sort_by string */
    /** @var $query string */


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
        [
            'label' => 'All',
        ],
        [
            'label' => 'Politics',

        ],
        [
            'label' => 'Social',

        ],
        [
            'label' => 'Technology',
        ],
        [
            'label' => 'Economy',

        ]


    ]
?>

    <div id = "left-sidebar">
        <?=
        SideNav::widget([
            'type' => SideNav::TYPE_DEFAULT,
            'heading' => 'Category',
            'items' => $category_items
        ]);
        ?>
    </div>
    <div class="col-md-12 col-xs-12  content">
        <div class='col-md-12' style="margin-left:0px; padding-left: 0px; ">
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
