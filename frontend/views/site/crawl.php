<?php
  use yii\helpers\Html;
?>

<?php $form = \yii\widgets\ActiveForm::begin(['action' => ['site/crawl'], 'method' => 'post']) ?>
  <div align="center">
    <?= Html::radioList('news',['cnn'], ['cnn' => 'CNN','bbc' => 'BBC', 'reuters' => 'Reuters', 'guardian' => 'Guardian', 'straits-times' => 'Strait Times'])?>
  <div id="below">
  </div>
    <br>

    <?= Html::textarea('accesscode', null, ['placeholder' => 'Facebook Access Token'])?>
    <br>
    <a href="https://developers.facebook.com/tools/explorer/145634995501895/"> Click to obtain Facebook Access Token </a>
    <br>
    <br>
  <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
  </div>

<?php \yii\widgets\ActiveForm::end() ?>