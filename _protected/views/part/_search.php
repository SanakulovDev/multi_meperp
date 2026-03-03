<?php
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\PartSearch */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="part-search">

	<?php $form = ActiveForm::begin([
		                                'action' => ['index'],
		                                'method' => 'get',
		                                'options' => [
			                                'data-pjax' => 1
		                                ],
	                                ]); ?>

	<?=$form->field($model, 'id')?>

	<?=$form->field($model, 'part_no')?>

	<?=$form->field($model, 'part_name')?>

	<?=$form->field($model, 'part_color')?>

	<?=$form->field($model, 'description')?>

	<div class="form-group">
		<?=Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary'])?>
		<?=Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default'])?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
