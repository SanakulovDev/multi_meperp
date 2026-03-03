<?php
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\Invoice */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="invoice-conttainer-form">

	<?php $form = ActiveForm::begin(); ?>

	<div class="row">
		<div class="col-lg-3">
			<?=$form->field($model, 'invoice_no')
			        ->textInput(['maxlength' => true])
			        ->label(Yii::t('app', 'Invoice')." №")?>
		</div>
	</div>

	<?=$this->render('__container-shipdt',
	                 [
		                 'errorlist' => $errorlist ?? null,
		                 'items' => $items,
		                 'model' => $model,
		                 'modelContainer' => $modelContainer,
		                 'modelContainerInvoice' => $modelContainerInvoice,
	                 ]
	)
	?>

	<div class="form-group pull-right">
		<?=Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
		<?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
