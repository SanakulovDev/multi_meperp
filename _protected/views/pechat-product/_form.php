<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PechatProduct */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pechat-product-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'part_id')->dropdownList($items, ['prompt'=>'----','class'=>'form-control select2']) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'color_id')->dropdownList($colorList, ['prompt'=>'----','class'=>'form-control select2']) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'number_lot')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'line')->textInput(['maxlength' => true]) ?>
        </div>
        
        <div class="col-md-2">
            <?= $form->field($model, 'date')->textInput(['type'=>'date', 'value'=>date('Y-m-d')]) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'weight_netto')->textInput() ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'weight_brutto')->textInput() ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'comment')->textInput(['maxlength' => true]) ?>
        </div>
    </div>






    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
