<?php
use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var $this          yii\web\View
 * @var $model         app\models\FreightInvoice
 * @var $form          yii\widgets\ActiveForm
 * @var $invoiceType   app\controllers\FreightInvoiceController
 * @var $routes        app\controllers\FreightInvoiceController
 * @var $deliveryTerms app\controllers\FreightInvoiceController
 * @var $currencies    app\controllers\FreightInvoiceController
 * @var $carriers      app\controllers\FreightInvoiceController
 */
$selectParams = ['prompt' => '. . .', 'class' => 'form-control select2 input-sm'];
?>

<div class="freight-invoice-form">
  <?php $form = ActiveForm::begin(); ?>
	<div class="row" style="margin-top:15px">
		<div class="col-sm-2">
			<label class="form-group has-float-label">
        <?=$form->field($model, 'invoice_type')->dropDownList($invoiceType, $selectParams);?>
			</label>
		</div>
		<div class="col-sm-4">
			<label class="form-group has-float-label">
        <?=$form->field($model, 'contract')->textInput(['maxlength' => true, 'class' => 'form-control input-sm'])->label(Yii::t('app','Contract number'))?>
			</label>
		</div>
		<div class="col-sm-3">
			<label class="form-group has-float-label">
        <?=$form->field($model, 'invoice_no')->textInput(['maxlength' => true, 'class' => 'form-control input-sm'])?>
			</label>
		</div>
		<div class="col-sm-3">
			<label class="form-group has-float-label">
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
        ]);?>
			</label>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-4">
			<label class="form-group has-float-label">
        <?=$form->field($model, 'route_id')->dropDownList($routes, $selectParams);?>
			</label>
		</div>
		<div class="col-sm-3">
			<label class="form-group has-float-label">
        <?=$form->field($model, 'carrier_id')->dropDownList($carriers, $selectParams);?>
			</label>
		</div>
		<div class="col-sm-3">
			<label class="form-group has-float-label">
        <?=$form->field($model, 'delivery_term_id')->dropDownList($deliveryTerms, $selectParams);?>
			</label>
		</div>
		<div class="col-sm-2">
			<label class="form-group has-float-label">
        <?=$form->field($model, 'currency_id')->dropDownList($currencies, $selectParams);?>
			</label>
		</div>
	</div>
	<div class="form-group pull-right">
    <?=Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
    <?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
	</div>

  <?php ActiveForm::end(); ?>

</div>
