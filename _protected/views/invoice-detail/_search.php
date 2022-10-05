<?php
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\InvoiceDetailSearch */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="invoice-detail-search">

	<?php $form = ActiveForm::begin([
		                                'action' => ['index'],
		                                'method' => 'get',
		                                'options' => [
			                                'data-pjax' => 1
		                                ],
	                                ]); ?>

	<?=$form->field($model, 'id')?>

	<?=$form->field($model, 'cont_inv_id')?>

	<?=$form->field($model, 'part_id')?>

	<?=$form->field($model, 'qty')?>

	<?=$form->field($model, 'price')?>

	<?php echo $form->field($model, 'currency_id') ?>

	<?php echo $form->field($model, 'remarks') ?>

	<div class="form-group">
		<?=Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary'])?>
		<?=Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default'])?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
