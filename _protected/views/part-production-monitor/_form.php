<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PartProductionMonitor */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="part-production-monitor-form">

  <?php $form = ActiveForm::begin(); ?>

  <?=$form->field($model, 'production_monitor_id')->textInput()?>

  <?=$form->field($model, 'part_id')->textInput()?>

  <?=$form->field($model, 'produced_qty')->textInput(['maxlength' => true])?>

  <?=$form->field($model, 'repaired_qty')->textInput(['maxlength' => true])?>

  <?=$form->field($model, 'broken_qty')->textInput(['maxlength' => true])?>

  <?=$form->field($model, 'actual_production_time')->textInput()?>

  <?=$form->field($model, 'created_at')->textInput()?>

  <?=$form->field($model, 'created_by')->textInput()?>

  <?=$form->field($model, 'updated_at')->textInput()?>

  <?=$form->field($model, 'updated_by')->textInput()?>

	<div class="form-group">
    <?=Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success'])?>
	</div>

  <?php ActiveForm::end(); ?>

</div>
