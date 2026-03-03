<?php

use app\models\Mold;
use app\models\ProductLine;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Machine */
/* @var $form yii\widgets\ActiveForm */
$mold = Mold::find()->all();
$moldList = ArrayHelper::map($mold, 'id', 'mold_no');

$zones = ProductLine::find()->where('is_zone = 1')->all();
$zoneItems = ArrayHelper::map($zones, 'id', 'linename');
?>

<div class="machine-form">

  <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-4 col-sm-4 col-lg-4">
          <?= $form->field($model, 'no')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-8 col-sm-8 col-lg-8">
          <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 col-sm-4 col-lg-4">
          <?= $form->field($model, 'product_line_id')->dropDownList($zoneItems); ?>
        </div>
        <div class="col-md-4 col-sm-4 col-lg-4">
          <?= $form->field($model, 'last_count')->textInput() ?>
        </div>
        <div class="col-md-4 col-sm-4 col-lg-4">
          <?= $form->field($model, 'sequence')->textInput() ?>
        </div>
    </div>

    <div class="">
      <?= Html::activeLabel($model, Yii::t('app', 'Molds')) ?>
      <?php
      $params = ['prompt' => '. . .', 'class' => 'form-control input-sm select2', 'multiple' => 'multiple', 'id' => 'moldId'];
      echo Html::dropDownList("moldName", $arr, $moldList, $params);
      ?>
    </div>
    <div class="row">
        <div class="col-md-4 col-sm-4 col-lg-4">
          <?php
          $mold = [];
          $items = ArrayHelper::map($mold, 'id', 'mold_no');
          $params = ['prompt' => '. . .', 'id' => 'subMoldId'];
          echo $form->field($model, 'mold_id')->dropDownList($items, $params);
          ?>
        </div>
    </div>

  <?php if (!$model->isNewRecord) : ?>
      <hr>
      <div class="row">
        <div class="col-sm-6 col-md-6 col-lg-6">
            <span class="form-control">
                <?= $model->createdBy->username . ' ' . $model->createdAtFormatted ?>
            </span>
        </div>
        <div class="col-sm-6 col-md-6 col-lg-6">
            <span class="form-control">
              <?= $model->updatedBy ? $model->updatedBy->username . ' ' . $model->updatedAtFormatted : '' ?>
            </span>
        </div>
      </div>
  <?php endif ?>

  <?php ActiveForm::end(); ?>
</div>

<script>
  var machine = <?php echo $model->id ? 1 : 0 ?>;
  var mold_id = <?php echo $model->mold_id ? $model->mold_id : 0 ?>;
  var mold = <?php echo $arr ? json_encode((object)$arr) : 0 ?>;
  var options = $('#subMoldId')
  $('#moldId').on('change', function (event) {
    $('#subMoldId').find('option:not(:first-child)').remove()
    var texts = $('#moldId :selected').map(function () {
      return $(this).text()
    }).get()

    $(this).val().forEach(function (element, index) {
      options.append(new Option(texts[index], element))
    })
  })

  if (machine) {
    var options = $('#subMoldId')
    var texts = $('#moldId :selected').map(function () {
      return $(this).text()
    }).get()

    size = Object.keys(mold).length

    for (var i = 0; i < size; i++) {
      options.append(new Option(texts[i], mold[i]))
    }
    $('#subMoldId').prop('value', mold_id)
  }
</script>