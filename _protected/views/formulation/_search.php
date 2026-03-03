<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\FormulationSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="formulation-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'formulation_base_id') ?>

    <?= $form->field($model, 'amount') ?>

    <?= $form->field($model, 'customer_id') ?>

    <?= $form->field($model, 'order_no') ?>

    <?php // echo $form->field($model, 'ulock') ?>

    <?php // echo $form->field($model, 'due_at') ?>

    <?php // echo $form->field($model, 'start_at') ?>

    <?php // echo $form->field($model, 'finish_at') ?>

    <?php // echo $form->field($model, 'act_rate') ?>

    <?php // echo $form->field($model, 'grind') ?>

    <?php // echo $form->field($model, 'packages') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
