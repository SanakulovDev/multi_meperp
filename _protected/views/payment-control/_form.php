<?php

use app\models\Contract;
use app\models\PaymentControl;
use kartik\datetime\DateTimePicker;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PaymentControl */
/* @var $form yii\widgets\ActiveForm */

$contracts = [];
$contractModels = [];
if ($model->isNewRecord == false) {
	$contractModels = Contract::find()->with(['currency'])->where(['supplier_id' => $model->supplier_id, 'status' => Contract::STATUS_ACTIVE])->all();
} else {
	if (count($suppliers) > 0) {
		$contractModels = Contract::find()->with(['currency'])->where(['supplier_id' => array_keys($suppliers)[0], 'status' => Contract::STATUS_ACTIVE])->all();
	}
}

$contractOptions = [];
foreach ($contractModels as $item) {
	$contracts[$item->id] = $item->contract_no;
	$contractOptions[$item->id] = [
		'data-currency' => $item->currency ? $item->currency->code : ''
	];
}

$validationUrl = ['validate'];
if (!$model->isNewRecord) {
	$validationUrl['id'] = $model->id;
}
$form = ActiveForm::begin([
	'id' => $model->formName(),
	'enableAjaxValidation' => true,
	'validateOnType' => false,
	'validationUrl' => $validationUrl,
	'options' => ['data-pjax' => true, 'class' => 'modalForm']
]);
?>

<div class="row">
	<div class="col-sm-6 col-md-6 col-lg-6">
		<?= $form->field($model, 'payment_type')->dropDownList($model->getTypes()) ?>
	</div>

	<div class="col-sm-6 col-md-6 col-lg-6">
      <?= $form->field($model, 'date')->widget(DateTimePicker::classname(), [
          'pluginOptions' => [
              'language' => Yii::$app->language,
              'autoclose' => true,
              'format' => 'yyyy-mm-dd',
              'minView' => 'month',
              'maxView' => 'month',
          ],
          'options' => [
              'autocomplete' => 'off'
          ]
      ]) ?>

	</div>

</div>

<div class="row">
	<div class="col-sm-6 col-md-6 col-lg-6">
      <?= $form->field($model, 'no')->textInput(['maxlength' => true]) ?>
	</div>
  <div class="col-sm-6 col-md-6 col-lg-6">
      <?= $form->field($model, 'amount')->textInput(['type' => 'number']) ?>
  </div>
</div>

<div class="row">
  <div class="col-sm-4 col-md-4 col-lg-4">
      <?= $form->field($model, 'supplier_id')->dropDownList($suppliers, ['class' => 'select2']) ?>
  </div>
  <div class="col-sm-4 col-md-4 col-lg-4">
      <?= $form->field($model, 'contract_id')->dropDownList($contracts, ['options' => $contractOptions]) ?>
  </div>
	<div class="col-sm-4 col-md-4 col-lg-4">
		<div class="form-group field-paymentcontrol-bank_name has-success">
			<label class="control-label" for="paymentcontrol-bank_name"><?= Yii::t('app', 'Currency') ?></label>
			<input type="text" id="paymentcontrol-currency" class="form-control" disabled="true" aria-invalid="false">
			<div class="help-block"></div>
		</div>
	</div>
</div>


<option>
</option>
<?php if ($model->isNewRecord == false) : ?>
	<div class="">
		<table class="table table-bordered table-condensed">
			<tr>
				<th><?= Yii::t('app', 'Created by') ?>
				</th>
				<th><?= Yii::t('app', 'Created at') ?>
				</th>
				<th><?= Yii::t('app', 'Updated by') ?>
				</th>
				<th><?= Yii::t('app', 'Updated at') ?>
				</th>
			</tr>
			<tr>
				<td><?= $model->createdBy->fullname ?>
				</td>
				<td><?= $model->createdAtFormatted ?>
				</td>
				<td><?= $model->updatedBy->fullname ?>
				</td>
				<td><?= $model->updatedAtFormatted ?>
				</td>
			</tr>
		</table>
	</div>
<?php endif; ?>

<?php ActiveForm::end(); ?>

<?php
$url = Url::to(['contract/list-by-supplier'], true);
$urlOrder = Url::to(['part-order/list-by-contract-id'], true);
$lcType = PaymentControl::LC_TYPE;
$postType = PaymentControl::POST_TYPE;
$preType = PaymentControl::PRE_TYPE;
$orderId = $model->part_order_id ?: 0;
$script = <<< JS
	$(function () {
		uiByType();
		var orderId = $orderId;
		contractId = $('#paymentcontrol-contract_id').find("option:first-child").val();
		if(contractId) {
			fillOrder(contractId);
			fillCurrency(contractId);
		}

		$('#paymentcontrol-supplier_id').on("select2:select", function(e) { 
			 var selectedSupplier = e.params.data;
			 var supplierGetUrl = '$url'+'?id='+selectedSupplier.id;
			 $.get(supplierGetUrl, function(data, status){
				// clear
				$('#paymentcontrol-contract_id')
					.find('option')
          .remove();
				$('#paymentcontrol-part_order_id')
					.find('option')
          .remove();
				$('#paymentcontrol-currency').val("")
				// append
				$.each(data, function () {
					var opt = new Option(this.text, this.id);
					opt.setAttribute("data-currency", this.currency);
        	$('#paymentcontrol-contract_id').append(opt)
				});

				contractId = $('#paymentcontrol-contract_id').find("option:first-child").val();
				if(contractId) {
					fillOrder(contractId);
					fillCurrency(contractId);
				}
			 });			 		 
		});

		function fillOrder(id) {
			 var orderUrl = '$urlOrder'+'?id='+id;			
			 $.get(orderUrl, function(data, status){
				$('#paymentcontrol-part_order_id')
					.find('option')
          .remove();
				$.each(data, function () {
					opt = new Option(this.text, this.id);
					if(orderId == this.id) opt.setAttribute('selected','selected');
					$('#paymentcontrol-part_order_id').append(opt);					
				});
			 }); 
			 $('#paymentcontrol-part_order_id').val(orderId);
			 fillCurrency(id);
		}	

		function uiByType(){
			var selType = parseInt($('#paymentcontrol-payment_type').val());
			switch (selType) {
				case 0:
					$('#paymentcontrol-expire_date').attr('disabled', false);
					$('#paymentcontrol-shipment_date').attr('disabled', false);
					$('#paymentcontrol-bank_name').attr('disabled', false);
				break;
				case 1:
					 $('#paymentcontrol-expire_date').attr('disabled', true);
					 $('#paymentcontrol-shipment_date').attr('disabled', true);
					 $('#paymentcontrol-bank_name').attr('disabled', true);
				break;
				case 2:
					$('#paymentcontrol-expire_date').attr('disabled', false);
					$('#paymentcontrol-shipment_date').attr('disabled', false);
					$('#paymentcontrol-bank_name').attr('disabled', true);
					break;	 
				 default:
					 break;
			 } 
		}

		$('#paymentcontrol-contract_id').on("change", function(e) { 
			 fillOrder(this.value);			 
		});

		$('#paymentcontrol-payment_type').on("change", function(e) { 
			//  selValue = this.value;			 
			 uiByType(this.value);
		});

		$('#paymentcontrol-part_order_id').on("change", function(e){
			orderId = this.value;
		});

		function fillCurrency(id) {
			var currency = $('#paymentcontrol-contract_id').find("option[value="+id+"]").attr("data-currency");
			$('#paymentcontrol-currency').val(currency);
		}

	});
JS;
$this->registerJs($script, yii\web\View::POS_END);
