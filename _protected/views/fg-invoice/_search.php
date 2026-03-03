<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\FgInvoiceSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="fg-invoice-search">

  <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
  ]); ?>

  <?=$form->field($model, 'id')?>
  <?=$form->field($model, 'factory_id')?>
  <?=$form->field($model, 'invoice_no')?>
  <?=$form->field($model, 'invoice_date')?>
  <?=$form->field($model, 'customer_id')?>
  <?php // echo $form->field($model, 'contract') ?>
  <?php // echo $form->field($model, 'rec_person_fullname') ?>
  <?php // echo $form->field($model, 'rec_person_regno') ?>
  <?php // echo $form->field($model, 'driver') ?>
  <?php // echo $form->field($model, 'truck') ?>
  <?php // echo $form->field($model, 'manager') ?>
  <?php // echo $form->field($model, 'account') ?>
  <?php // echo $form->field($model, 'sender') ?>
  <?php // echo $form->field($model, 'vat') ?>
  <?php // echo $form->field($model, 'excise') ?>
  <?php // echo $form->field($model, 'created_at') ?>
  <?php // echo $form->field($model, 'created_by') ?>
  <?php // echo $form->field($model, 'updated_at') ?>
  <?php // echo $form->field($model, 'updated_by') ?>
  <div class="form-group">
    <?=Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary'])?>
    <?=Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary'])?>
  </div>

  <?php ActiveForm::end(); ?>

</div>
