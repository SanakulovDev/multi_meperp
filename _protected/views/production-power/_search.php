<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProductionPowerSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="production-power-search">

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

    <?= $form->field($model, 'test_pr') ?>

    <?= $form->field($model, 'target_date') ?>

    <?php  echo $form->field($model, 'line') ?>


    <?php  echo $form->field($model, 'unitId') ?>

    <?php  echo $form->field($model, 'plan_power') ?>

    <?php  echo $form->field($model, 'max_power') ?>

    <?php  echo $form->field($model, 'special') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
