<?php
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\FgInvoiceDetailSearch */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="fg-invoice-detail-search">

	<?php $form = ActiveForm::begin([
		                                'action' => ['index'],
		                                'method' => 'get',
	                                ]); ?>

	<?=$form->field($model, 'id')?>

	<?=$form->field($model, 'fg_invoice_id')?>

	<?=$form->field($model, 'part_no')?>

	<?=$form->field($model, 'part_name')?>

	<?=$form->field($model, 'qty')?>

	<?php // echo $form->field($model, 'price') ?>

	<?php // echo $form->field($model, 'vat') ?>

	<?php // echo $form->field($model, 'excise') ?>

	<?php // echo $form->field($model, 'unit_id') ?>

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
