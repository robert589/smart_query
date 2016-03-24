<?php
?>
<div style="font-size: 15px; font-weight:bold"><?= $model['message'] ?></div>
<br>
<img src="<?= $model['media']?>" style="width:100%; max-height:500px">
<br>
<br>

<?= 'Source: ' . \yii\helpers\Html::a($model['source'], $model['link_source'], ['target ' => '_blank']) ?>
<br>
<?= 'Category: ' . $model['category'] ?>
<br>
<?= '<b>Created at:</b> ' . date("j F Y, H:i:s", $model['created_time']) ?>
<hr>
<br>