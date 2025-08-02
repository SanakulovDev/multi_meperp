<?php
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\InvoicePartProblemSearch */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="invoice-part-problem-search">

	<?php $form = ActiveForm::begin([
		                                'action' => ['index'],
		                                'method' => 'get',
		                                'options' => [
			                                'data-pjax' => 1
		                                ],
	                                ]); ?>

	<?=$form->field($model, 'id')?>

	<?=$form->field($model, 'inv_detail_id')?>

	<?=$form->field($model, 'part_order_no')?>

	<?=$form->field($model, 'contract_no')?>

	<?=$form->field($model, 'created_by')?>

	<?php // echo $form->field($model, 'created_at') ?>

	<?php // echo $form->field($model, 'updated_by') ?>

	<?php // echo $form->field($model, 'updated_at') ?>

	<div class="form-group">
		<?=Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary'])?>
		<?=Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary'])?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
