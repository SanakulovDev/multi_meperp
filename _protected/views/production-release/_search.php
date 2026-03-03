<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProductionReleaseSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="production-release-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'part_id') ?>

    <?= $form->field($model, 'part_name') ?>

    <?= $form->field($model, 'line') ?>

    <?= $form->field($model, 'pr_order_number') ?>

    <?php // echo $form->field($model, 'target_date') ?>

    <?php // echo $form->field($model, 'shift') ?>

    <?php // echo $form->field($model, 'time') ?>

    <?php // echo $form->field($model, 'quantity') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
