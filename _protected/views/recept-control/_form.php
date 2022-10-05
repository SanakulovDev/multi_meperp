<?php
use app\models\Contract;
use app\models\ReceptControl;
use app\models\SalesContract;
use kartik\datetime\DateTimePicker;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ReceptControl */
/* @var $form yii\widgets\ActiveForm */
/* @var $customers */

$contracts = [];
$contractModels = [];
if ($model->isNewRecord == false) {
	$contractModels = SalesContract::find()->with(['currency'])->where(['customer_id' => $model->customer_id, 'status' => SalesContract::STATUS_ACTIVE])->all();
} else {
	if (count($customers) > 0) {
		$contractModels = SalesContract::find()->with(['currency'])->where(['customer_id' => array_keys($customers)[0], 'status' => SalesContract::STATUS_ACTIVE])->all();
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
		<?= $form->field($model, 'payment_term')->dropDownList($model->getTypes()) ?>
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
      <?= $form->field($model, 'customer_id')->dropDownList($customers, ['class' => 'select2']) ?>
  </div>
  <div class="col-sm-4 col-md-4 col-lg-4">
      <?= $form->field($model, 'sales_contract_id')->dropDownList($contracts, ['options' => $contractOptions]) ?>
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
$url = Url::to(['sales-contract/list-by-sales-supplier'], true);
$script = <<< JS
	$(function () {
		contractId = $('#receptcontrol-sales_contract_id').find("option:first-child").val();
		if(contractId) {
			fillCurrency(contractId);
		}

		$('#receptcontrol-customer_id').on("select2:select", function(e) { 
			 var selectedSupplier = e.params.data;
			 var supplierGetUrl = '$url'+'?id='+selectedSupplier.id;
			 $.get(supplierGetUrl, function(data, status){
				// clear
				$('#receptcontrol-sales_contract_id')
					.find('option')
          .remove();
				$('#receptcontrol-currency').val("")
				// append
				$.each(data, function () {
					var opt = new Option(this.text, this.id);
					opt.setAttribute("data-currency", this.currency);
        	$('#receptcontrol-sales_contract_id').append(opt)
				});

				contractId = $('#receptcontrol-sales_contract_id').find("option:first-child").val();
				if(contractId) {
					fillCurrency(contractId);
				}
			 });			 		 
		});	

		function fillCurrency(id) {
			var currency = $('#receptcontrol-sales_contract_id').find("option[value="+id+"]").attr("data-currency");
			$('#receptcontrol-currency').val(currency);
		}

	});
JS;
$this->registerJs($script, yii\web\View::POS_END);
