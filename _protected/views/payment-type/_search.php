<?php
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\PaymentTypeSearch */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-type-search">

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

	<?=$form->field($model, 'created_by')?>

	<?=$form->field($model, 'created_at')?>

	<?php // echo $form->field($model, 'updated_by') ?>

	<?php // echo $form->field($model, 'updated_at') ?>

	<div class="form-group">
		<?=Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary'])?>
		<?=Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary'])?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
