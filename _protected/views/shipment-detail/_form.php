<?php

use app\components\Helpers;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ShipmentDetail */
/* @var $form yii\widgets\ActiveForm */

$validationUrl = ['validate'];
if(!$model->isNewRecord){
    $validationUrl['id'] = $model->id;
}
?>

<style>

.text-nowrap {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

</style>

<div class="shipment-detail-form">

    <?php $form = ActiveForm::begin([
        'id' => $model->formName(),
        'enableAjaxValidation' => true,
        'validateOnType' => false,
        'validationUrl' => $validationUrl,
        'options' => ['data-pjax' => true, 'class' => 'modalForm']
    ]); ?>

<div class="row">
    <div class="form-group col-sm-4 col-md-4 col-lg-4">
        <label class="control-label">
            <?=$model->getAttributeLabel('part_id')?>
        </label>
        <label class="form-control text-nowrap"><?=$model->part->partinfo?></label>
    </div>
    <div class="form-group col-sm-4 col-md-4 col-lg-4">
        <label class="control-label">
            <?=$model->getAttributeLabel('part_name')?>
        </label>
        <label class="form-control text-nowrap"><?=$model->part->part_name?></label>
    </div>
    <div class="form-group col-sm-4 col-md-4 col-lg-4">
        <label class="control-label">
            <?=$model->getAttributeLabel('supplier_id')?>
        </label>
        <label class="form-control text-nowrap"><?=$model->supplier->name ?? ''?></label>
    </div>
</div>
<div class="row">
    <div class="form-group col-sm-2 col-md-2 col-lg-2">
        <label class="control-label text-right">
            <?=$model->getAttributeLabel('pack_size')?>
        </label>
        <label class="form-control"><?=Helpers::numberFormatRemoveZero($model->pack_size,2,'.',' ')?></label>
    </div>
    <div class="form-group col-sm-2 col-md-2 col-lg-2">
        <label class="control-label">
            <?=$model->getAttributeLabel('model')?>
        </label>
        <label class="form-control"><?=$model->part->productModel->modelname ?? '';?></label>
    </div>
    <div class="form-group col-sm-2 col-md-2 col-lg-2">
        <label class="control-label">
            <?=$model->getAttributeLabel('unit')?>
        </label>
        <label class="form-control"><?=$model->part->unit->unit_value?></label>
    </div>
    <div class="form-group col-sm-2 col-md-2 col-lg-2">
        <label class="control-label">
            <?=$model->getAttributeLabel('disruption_date')?>
        </label>
        <label class="form-control"><?=date('d M', strtotime($model->disruption_date))?></label>
    </div>
    <div class="form-group col-sm-4 col-md-4 col-lg-4">
        <label class="control-label">
            <?=$model->getAttributeLabel('need_qty')?>
        </label>
        <label class="form-control"><?=Helpers::numberFormatRemoveZero($model->need_qty,2,'.',' ')?></label>
    </div>
</div>
<div class="row">
    <div class="form-group col-sm-4 col-md-4 col-lg-4">
        <?= $form->field($model, 'ready_qty')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="form-group col-sm-4 col-md-4 col-lg-4">
        <label class="control-label">
            <?=$model->getAttributeLabel('diff_ready_need')?>
        </label>
        <label class="form-control"><?=Helpers::numberFormatRemoveZero($model->diffReadyNeed ?? 0,2,'.',' ')?></label>
    </div>
</div>
<div class="row">
    <div class="form-group col-sm-4 col-md-4 col-lg-4">
        <?= $form->field($model, 'approved_qty')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="form-group col-sm-4 col-md-4 col-lg-4">
        <label class="control-label">
            <?=$model->getAttributeLabel('diff_appr_ready')?>
        </label>
        <label class="form-control"><?=Helpers::numberFormatRemoveZero($model->diffApprReady ?? 0,2,'.',' ')?></label>
    </div>
</div>
<div class="row">
    <div class="form-group col-sm-12 col-md-12 col-lg-12">
        <?= $form->field($model, 'comment')->textarea() ?>
    </div>
</div>

    <?php ActiveForm::end(); ?>

</div>
