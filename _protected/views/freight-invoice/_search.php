<?php
use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var $this          yii\web\View
 * @var $model         app\models\FreightInvoiceSearch
 * @var $form          yii\widgets\ActiveForm
 * @var $routes        app\controllers\FreightInvoiceController
 * @var $deliveryTerms app\controllers\FreightInvoiceController
 * @var $carriers      app\controllers\FreightInvoiceController
 */
$selectParams = ['prompt' => '. . .', 'class' => 'form-control select2 input-sm'];
?>

<div class="freight-invoice-search">
  <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    'options' => [
      'data-pjax' => 1
    ],
  ]); ?>
	<div class="row">
		<div class="col-sm-4">
      <?=$form->field($model, 'contract')->textInput(['maxlength' => true, 'class' => 'form-control input-sm'])?>
		</div>
		<div class="col-sm-4">
      <?=$form->field($model, 'invoice_no')->textInput(['maxlength' => true, 'class' => 'form-control input-sm'])?>
		</div>
		<div class="col-sm-4">
      <?=$form->field($model, 'invoice_date')->widget(DateTimePicker::classname(), [
        'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
        'layout' => '{picker}{input}{remove}',
        'removeButton' => ['position' => 'append'],
        'language' => 'ru',
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
          'class' => 'form-control input-sm'
        ]
      ]);
      ?>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-4">
      <?=$form->field($model, 'route_id')->dropDownList($routes, $selectParams);?>
		</div>
		<div class="col-sm-4">
      <?=$form->field($model, 'carrier_id')->dropDownList($carriers, $selectParams);?>
		</div>
		<div class="col-sm-4">
      <?=$form->field($model, 'delivery_term_id')->dropDownList($deliveryTerms, $selectParams);?>
		</div>
	</div>
	<div class="form-group">
    <?=Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary btn-sm'])?>
	</div>
  <?php ActiveForm::end(); ?>
</div>
