<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\InvoicePayment */
/* @var $form yii\widgets\ActiveForm */
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
			<div class="form-group field-invoice-invoice_no has-success">
				<label class="control-label" for="invoice-invoice_no"><?=$model->getAttributeLabel('invoice_no') ?></label>
				<label class="form-control"><?=$model->invoice_no?></label>
			</div>
    </div>
		<div class="col-sm-6 col-md-6 col-lg-6">
        <?= $form->field($model, 'invoice_date')->textInput(['type'=>'date']) ?>
    </div>
</div>
<div class="row">
		<div class="col-sm-6 col-md-6 col-lg-6">
        <?= $form->field($model, 'invoice_amount')->textInput(['maxlength' => true]) ?>
    </div>
		<div class="col-sm-6 col-md-6 col-lg-6">
        <?= $form->field($model, 'currency_id')->dropDownList($currencies) ?>
    </div>
</div>
<br>
<?php if(!$model->isNewRecord): ?>
	<div class="row">
		<div class="col-sm-6 col-md-6 col-lg-6">
			<span class="form-control">
				<?=$model->updatedBy ? $model->updatedBy->fullname : ''?>
			</span>
		</div>
		<div class="col-sm-6 col-md-6 col-lg-6">
			<span class="form-control">
				<?=$model->updatedAtFormatted?>
			</span>
		</div>
	</div>
<?php endif ?>
<?php ActiveForm::end(); ?>