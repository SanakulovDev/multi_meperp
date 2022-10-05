<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OemPlan */
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
    <div class="col-sm-4 col-md-4 col-lg-4">
        <?= $form->field($model, 'model_id')->dropDownList($models,['disabled'=>!$model->isNewRecord]) ?>
    </div>
    <div class="col-sm-4 col-md-4 col-lg-4">
        <?= $form->field($model, 'target_date')->textInput(['type'=>'date', 'disabled'=>!$model->isNewRecord]) ?>
    </div>
    <div class="col-sm-4 col-md-4 col-lg-4">
        <?= $form->field($model, 'quantity')->textInput(['type'=>'number']) ?>
    </div>        
</div>

<?php ActiveForm::end(); ?>

