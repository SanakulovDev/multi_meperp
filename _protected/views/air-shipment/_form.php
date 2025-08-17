<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model app\models\AirShipment */
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
        <?=$form->field($model, 'supplier_id')->dropDownList($suppliers, ['class' => 'form-control select2'])?>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-6">
        <?= $form->field($model, 'volume')->textInput(['maxlength' => true]) ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-6 col-md-6 col-lg-6">
        <?= $form->field($model, 'cost')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-6">
        <?= $form->field($model, 'period')->textInput(['type' => 'month']) ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-6 col-md-6 col-lg-6">
        <?=$form->field($model, 'air_shipment_reason_id')->dropDownList($reasons, ['class' => 'form-control select2'])?>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-6">
        <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>
    </div>
</div>
<?php if (!$model->isNewRecord): ?>
<div class="row">
    <div class="col-sm-6 col-md-6 col-lg-6">
        <span class="form-control">
            <?=$model->getAttributeLabel('created_by') . ' ' . $model->createdBy->fullname . ' ' . $model->createdAtFormatted?>
        </span>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-6">
        <span class="form-control">
            <?=$model->getAttributeLabel('updated_by') . ' ' . $model->updatedBy->fullname . ' ' . $model->updatedAtFormatted?>
        </span>
    </div>
</div>
<?php endif ?>
<?php ActiveForm::end();
