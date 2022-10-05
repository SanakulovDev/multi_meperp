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
    <div class="col-sm-4 col-md-4 col-lg-4">
        <?= $form->field($model, 'invoice_id')->dropDownList($invoices, ['class'=>'form-control select2']) ?>
    </div>
    <div class="col-sm-4 col-md-4 col-lg-4">
        <?= $form->field($model, 'payment_control_id')->dropDownList($payments, ['class'=>'form-control select2']) ?>
    </div>
    <div class="col-sm-4 col-md-4 col-lg-4">
        <?= $form->field($model, 'amount')->textInput(['maxlength' => true]) ?>
    </div>
</div>

<?php if(!$model->isNewRecord): ?>
	<div class="row">
		<div class="col-sm-6 col-md-6 col-lg-6">
			<span class="form-control">
				<?=$model->updatedBy->fullname?>
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