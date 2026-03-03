<?php
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\SupplierSearch */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="supplier-search">

	<?php $form = ActiveForm::begin([
		                                'action' => ['index'],
		                                'method' => 'get',
		                                'options' => [
			                                'data-pjax' => 1
		                                ],
	                                ]); ?>

	<?=$form->field($model, 'id')?>

	<?=$form->field($model, 'name')?>

	<?=$form->field($model, 'duns')?>

	<?=$form->field($model, 'alias')?>

	<?=$form->field($model, 'address')?>

	<?php // echo $form->field($model, 'city') ?>

	<?php // echo $form->field($model, 'postal') ?>

	<?php // echo $form->field($model, 'country') ?>

	<?php // echo $form->field($model, 'country_code') ?>

	<?php // echo $form->field($model, 'contact_name') ?>

	<?php // echo $form->field($model, 'contact_position') ?>

	<?php // echo $form->field($model, 'contact_email') ?>

	<?php // echo $form->field($model, 'contact_phone') ?>

	<?php // echo $form->field($model, 'contact_cellular') ?>

	<div class="form-group">
		<?=Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary'])?>
		<?=Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default'])?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
