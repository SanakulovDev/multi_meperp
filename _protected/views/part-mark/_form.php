<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PartColor */
/* @var $form yii\widgets\ActiveForm */
?>

<?php
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

<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

<?php ActiveForm::end(); ?>