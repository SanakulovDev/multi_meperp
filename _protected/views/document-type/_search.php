<?php
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\DocumentTypeSearch */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="document-type-search">

	<?php $form = ActiveForm::begin([
		                                'action' => ['index'],
		                                'method' => 'get',
	                                ]); ?>

	<?=$form->field($model, 'id')?>

	<?=$form->field($model, 'code')?>
	<?=$form->field($model, 'name')?>
	<?=$form->field($model, 'description')?>

	<!--    --><? //= $form->field($model, 'yyyy') ?>
	<!--    --><? //= $form->field($model, 'sequence') ?>
	<?php // echo $form->field($model, 'created_by') ?>
	<?php // echo $form->field($model, 'created_at') ?>
	<?php // echo $form->field($model, 'updated_by') ?>
	<?php // echo $form->field($model, 'updated_at') ?>

	<div class="form-group">
		<?=Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary'])?>
		<?=Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default'])?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
