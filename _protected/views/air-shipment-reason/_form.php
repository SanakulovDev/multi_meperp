<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AirShipmentReason */
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

<?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

<?php if(!$model->isNewRecord): ?>
	<div class="row">
		<div class="col-sm-6 col-md-6 col-lg-6">
			<span class="form-control">
				<?=$model->getAttributeLabel('created_by').' '.$model->createdBy->fullname.' '.$model->createdAtFormatted?>
			</span>
		</div>
		<div class="col-sm-6 col-md-6 col-lg-6">
			<span class="form-control">
				<?=$model->getAttributeLabel('updated_by').' '.$model->updatedBy->fullname.' '.$model->updatedAtFormatted?>
			</span>
		</div>
	</div>
<?php endif ?>
<?php ActiveForm::end(); ?>