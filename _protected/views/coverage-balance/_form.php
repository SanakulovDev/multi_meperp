<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CoverageBalance */
/* @var $form yii\widgets\ActiveForm */

$validationUrl = ['validate'];
if(!$model->isNewRecord){
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
    <div class="form-group col-sm-6 col-md-6 col-lg-6">
        <label class="control-label">
            <?=$model->getAttributeLabel('supplier_id')?>
        </label>
        <label class="form-control"><?=$model->supplier->name?></label>
    </div>
    <div class="form-group col-sm-6 col-md-6 col-lg-6">
        <label class="control-label">
            <?=$model->getAttributeLabel('payment_term_id')?>
        </label>
        <label class="form-control"><?=$model->paymentTerm->name?></label>
    </div>
</div>
    
<div class="row">
    <div class="form-group col-sm-4 col-md-4 col-lg-4">
        <label class="control-label">
            <?=$model->getAttributeLabel('period')?>
        </label>
        <label class="form-control"><?=$model->periodMonth?></label>
    </div>

    <div class="form-group col-sm-4 col-md-4 col-lg-4">
        <label class="control-label">
            <?=$model->getAttributeLabel('debt')?>
        </label>
        <label class="form-control"><?=number_format(round($model->debt,0),0,'.',' ')?></label>
    </div>
    <div class="col-sm-4 col-md-4 col-lg-4">
        <?= $form->field($model, 'paid')->textInput(['maxlength' => true,'autofocus' => true]) ?>
    </div>    
</div>
    

<?php ActiveForm::end(); ?>