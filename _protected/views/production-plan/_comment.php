<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProductionPlanComment */
/* @var $form yii\widgets\ActiveForm */

$validationUrl = ['validate-comment'];
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
<?//= $form->field($model, 'production_plan_id')->textInput(['type' => 'hidden'])->label(false) ?>
<?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>
<?php ActiveForm::end(); ?>
