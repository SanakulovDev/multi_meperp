<?php
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\UlocSearch */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="uloc-search">

	<?php $form = ActiveForm::begin([
		                                'action' => ['index'],
		                                'method' => 'get',
		                                'options' => [
			                                'data-pjax' => 1
		                                ],
	                                ]); ?>

	<?=$form->field($model, 'id')?>

	<?=$form->field($model, 'title')?>

	<?=$form->field($model, 'description')?>

	<?=$form->field($model, 'line_id')?>

	<?=$form->field($model, 'min_stock')?>

	<?php // echo $form->field($model, 'max_stock') ?>

	<?php // echo $form->field($model, 'actual_stock') ?>

	<?php // echo $form->field($model, 'status') ?>

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
