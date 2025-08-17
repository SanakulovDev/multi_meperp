<?php
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\ProductionOrderSearch */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="production-order-search">

	<?php $form = ActiveForm::begin([
		                                'action' => ['index'],
		                                'method' => 'get',
	                                ]); ?>

	<?=$form->field($model, 'id')?>

	<?=$form->field($model, 'part_id')?>

	<?=$form->field($model, 'serial_number')?>

	<?=$form->field($model, 'current_event')?>

	<?=$form->field($model, 'current_seq')?>

	<?php // echo $form->field($model, 'is_printed') ?>

	<?php // echo $form->field($model, 'quantity') ?>

	<?php // echo $form->field($model, 'created_by') ?>

	<?php // echo $form->field($model, 'updated_by') ?>

	<?php // echo $form->field($model, 'created_at') ?>

	<?php // echo $form->field($model, 'updated_at') ?>

	<div class="form-group">
		<?=Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary'])?>
		<?=Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary'])?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
