<?php
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\ContainerInvoiceSearch */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="container-invoice-search">

	<?php $form = ActiveForm::begin([
		                                'action' => ['index'],
		                                'method' => 'get',
		                                'options' => [
			                                'data-pjax' => 1
		                                ],
	                                ]); ?>

	<?=$form->field($model, 'container_id')?>

	<?=$form->field($model, 'invoice_id')?>

	<?=$form->field($model, 'shipped_at')?>

	<?=$form->field($model, 'shipped_by')?>

	<?=$form->field($model, 'arrived_at')?>

	<?php // echo $form->field($model, 'arrived_by') ?>

	<div class="form-group">
		<?=Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary'])?>
		<?=Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default'])?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
