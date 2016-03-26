<?php
    use yii\helpers\Html;
    /** @var $model array */
    if($model['source'] == 'BBC'){
        $source_logo = Yii::$app->request->baseUrl . '/frontend/web/img/download.png';
    }
    else if($model['source'] == 'Guardian'){
        $source_logo = Yii::$app->request->baseUrl . '/frontend/web/img/guardians.jpg';
    }

    else if($model['source'] == 'CNN'){
        $source_logo = Yii::$app->request->baseUrl . '/frontend/web/img/cnn.jpe';
    }

    else if($model['source'] == 'Reuters'){
        $source_logo = Yii::$app->request->baseUrl . '/frontend/web/img/reuters.jpe';
    }

    else if($model['source'] == 'Straits Times'){
        $source_logo = Yii::$app->request->baseUrl . '/frontend/web/img/straits.png';
    }
    if(isset($model['attachment_name'])){
        $attc_name =     $model['attachment_name'] ;

    }
    else{
        $attc_name = '';
    }
    $exploded = explode('_' ,$model['post_id']);
    $link_to_fb = 'https://facebook.com/' . $exploded[0]  . '/posts/' . $exploded[1];
?>

<div class="col-md-12" style="margin-bottom: 50px; border-bottom: 1px solid #E5E5E5">
    <div class="col-md-12 marginer" style="margin-bottom: 15px">
        <div class="col-md-2 marginer">
            <img style="width:100%" src="<?= $source_logo ?>">
        </div>
        <div class="col-md-6">
            <?= Html::a($model['source'], $model['attachment_media_href']) ?> | <?= $model['category'] ?>
            <br>
            <?= '<span class="glyphicon glyphicon-time"></span> ' . date("j F Y, H:i:s", $model['created_time']) ?>

        </div>
        <div class="col-md-4 marginer" style="font-size: 2em">
            <span class="glyphicon glyphicon-thumbs-up"> <?='  '.  $model['like_count'] ?> </span>
        </div>

    </div>

    <div class="col-md-12 marginer" style="margin-bottom: 15px">
        <div style="font-size: 25px; font-weight:bold"><?= Html::a($model['message'], $link_to_fb, ['target' => '_blank']) ?></div>

    </div>

    <div class="col-md-12 marginer" style="margin-right:0; margin-bottom: 30px">
        <img src="<?= $model['attachment_media_fullsize_src']?>" class="news_image"  id="image">
        <div class="below_image" align="center">
            <?= $attc_name ?>
        </div>
    </div>


</div>


