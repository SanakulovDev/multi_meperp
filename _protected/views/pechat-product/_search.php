<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PechatProductSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pechat-product-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'part_id') ?>

    <?= $form->field($model, 'number_lot') ?>

    <?= $form->field($model, 'date') ?>

    <?= $form->field($model, 'weight_netto') ?>

    <?php // echo $form->field($model, 'weight_brutto') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
