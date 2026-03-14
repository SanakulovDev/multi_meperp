<?php

use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Waybill */
/* @var $form yii\widgets\ActiveForm */
/* @var $factory_items app\Controllers\WaybillController */

?>

<div class="waybill-form">

  <?php $form = ActiveForm::begin(); ?>

  <div class="row">

    <div class="col-md-4 col-sm-4 col-lg-4">
      <?= $form->field($model, 'factory_id')->dropDownList($factory_items, [ 'id' => 'active-factory', 'class' => 'form-control']); ?>
    </div>

    <div class="col-md-4 col-sm-4 col-lg-4">
      <?= $form->field($model, 'waybill_no')
        ->label(Yii::t('app', 'FG Invoice no'))
        ->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-md-4 col-sm-4 col-lg-4">
      <?= $form->field($model, 'waybill_date')
        ->label(Yii::t('app', 'FG Invoice date'))
        ->widget(DateTimePicker::class, [
          'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
          'layout' => '{picker}{input}{remove}',
          'removeButton' => ['position' => 'append'],
          'language' => Yii::$app->language,
          'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd',
            'startView' => 'month',
            'minView' => 'month',
            'maxView' => 'month',
          ],
          'options' => [
            'autocomplete' => 'off',
            'placeholder' => 'YYYY-MM-DD',
            'class' => 'form-control'
          ]
        ]);
      ?>
    </div>

  </div>

  <div class="row">

    <div class="col-md-12 col-sm-12 col-lg-12">
      <?= $form->field($model, 'invoices')->dropDownList($items ?? [], ['class' => 'form-control select2', 'multiple' => true])->label(Yii::t('app', 'FG Invoice no (TTN)')); ?>
    </div>

  </div>

  <?php if (isset($errorlist) && count($errorlist)) : ?>
    <div class="alert-danger alert fade in">
      <strong><?= Yii::t('app', 'Error') ?></strong>
      <?php foreach ($errorlist as $err_index => $err_value) : ?>
        <p><strong><?= $err_index ?>:</strong> <?= $err_value ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <div class="form-group pull-right">

    <?= Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm']) ?>
    <?= Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm']) ?>

  </div>

  <?php ActiveForm::end(); ?>
<input type="hidden" id="isNewRecord" name="isNewRecord" value="<?= ($model->isNewRecord) ? 1 : 0 ?>">
<input type="hidden" id="selectedInvoices" name="selectedInvoices" value="<?= (is_array($model->invoices)) ? json_encode($model->invoices) : '{}'?>">
</div>

<?php
$url = Url::to(['waybill/factory-info']);
$add_item = <<< JS

	$(document).ready(function() {	 

      $(document).on('change', '#active-factory', function(){

        let url = '$url'+'?id='+$('#active-factory').val()+'&isNewRecord='+$('#isNewRecord').val();

        $.get(url, function(data, status){

          if($('#isNewRecord').val() == 1){
            $('#waybill-waybill_no').val(data.waybill_no);
          }
          
          $('#waybill-invoices').empty();
          $('#waybill-invoices').select2({ "data": data.items });
          $('#waybill-invoices').val(JSON.parse($('#selectedInvoices').val()));

         });

      });

      $('#active-factory').trigger("change");

	});

JS;

$this->registerJs($add_item, yii\web\View::POS_END);
?>