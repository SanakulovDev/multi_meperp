<?php
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\InvoiceSearch */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="invoice-search">

	<?php $form = ActiveForm::begin([
		                                'action' => ['index'],
		                                'method' => 'get',
	                                ]); ?>

	<?=$form->field($model, 'id')?>

	<?=$form->field($model, 'invoice_no')?>

	<?=$form->field($model, 'shipper')?>

	<!--    --><? //= $form->field($model, 'port_of_loading') ?>

	<!--    --><? //= $form->field($model, 'package_qty') ?>

	<?php // echo $form->field($model, 'cbm') ?>

	<?php // echo $form->field($model, 'n_weight') ?>

	<?php // echo $form->field($model, 'g_weight') ?>

	<?php // echo $form->field($model, 'total_amount') ?>

	<?php // echo $form->field($model, 'created_at') ?>

	<?php // echo $form->field($model, 'updated_at') ?>

	<?php // echo $form->field($model, 'created_by') ?>

	<?php // echo $form->field($model, 'updated_by') ?>

	<div class="form-group">
		<?=Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary'])?>
		<?=Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default'])?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
