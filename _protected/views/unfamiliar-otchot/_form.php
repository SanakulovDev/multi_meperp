<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UnfamiliarOtchot */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="unfamiliar-otchot-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'part_id')->dropDownList($list, ['prompt'=>'------']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'quantity')->textInput(['type'=>'number']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'location')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'status')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'expected_arrival_date')->textInput(['type'=>'date']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    

    





    <?php ActiveForm::end(); ?>

</div>
