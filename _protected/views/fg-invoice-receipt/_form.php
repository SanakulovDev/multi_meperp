<?php
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ReceptControl */
/* @var $form yii\widgets\ActiveForm */
/* @var $fgInvoices */
/* @var $customers */

$contracts = [];
$contractModels = [];

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
	<div class="col-sm-4 col-md-4 col-lg-4">
		<?= $form->field($model, 'fg_invoice_id')->dropDownList($fgInvoices, ['class' => 'select2']) ?>
	</div>
	<div class="col-sm-4 col-md-4 col-lg-4">
		<?= $form->field($model, 'recept_control_id')->dropDownList([], ['class' => 'select2']) ?>
	</div>
  <div class="col-sm-4 col-md-4 col-lg-4">
    <?= $form->field($model, 'amount')->textInput(['type' => 'number']) ?>
  </div>
</div>

<div class="row">
	<div class="col-sm-6 col-md-6 col-lg-6">
    <div class="form-group field-paymentcontrol-bank_name has-success">
      <label class="control-label" for="paymentcontrol-bank_name"><?= Yii::t('app', 'Currency') ?></label>
      <input type="text" id="fg-invoice-receipt-customer" class="form-control" disabled="true" aria-invalid="false">
      <div class="help-block"></div>
    </div>
	</div>
  <div class="col-sm-6 col-md-6 col-lg-6">
    <div class="form-group field-paymentcontrol-bank_name has-success">
      <label class="control-label" for="paymentcontrol-bank_name"><?= Yii::t('app', 'Currency') ?></label>
      <input type="text" id="fg-invoice-receipt-contract" class="form-control" disabled="true" aria-invalid="false">
      <div class="help-block"></div>
    </div>
  </div>
</div>

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
$url = Url::to(['recept-control/list-by-fg-invoice'], true);
$isUpdate = $model->isNewRecord ? 0: 1;
$fgInvoiceId = $model->fg_invoice_id ?? 0;
$receptControlId = $model->recept_control_id ?? 0;
$script = <<< JS
	$(function () {
		function loadData(fgInvoiceId, selected=null) {
		  var url = '$url'+'?id='+fgInvoiceId;
		  $.get(url, function(data, status){
				$('#fginvoicereceipt-recept_control_id')
					.find('option')
          .remove();
				$('#fg-invoice-receipt-customer').val(data.customer.name)
				$('#fg-invoice-receipt-contract').val(data.contract.contract_no)
				$.each(data.receipts, function () {
					var opt = new Option(this.text, this.id);
					opt.setAttribute("data-amount", this.amount);
        	$('#fginvoicereceipt-recept_control_id').append(opt)
				});
				
				if(selected) $('#fginvoicereceipt-recept_control_id').val(selected)
			 });
		}
	  
	  $('#fginvoicereceipt-fg_invoice_id').on("select2:select", function(e) { 
			 var fgInvoice = e.params.data;
			 loadData(fgInvoice.id);
		});
		
		if($isUpdate === 1) {
		  loadData($fgInvoiceId, $receptControlId);
		} else {
		  const firstOpt = $('#fginvoicereceipt-fg_invoice_id option:first-child');
		  loadData(firstOpt.val());
		}
	});
JS;
$this->registerJs($script, yii\web\View::POS_END);
