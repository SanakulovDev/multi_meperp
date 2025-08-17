<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\FormulationComponent */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="formulation-component-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'formulation_id')->textInput() ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'part_id')->textInput() ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'std_value')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'actual_value')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
