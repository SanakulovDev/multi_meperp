<?php
	use app\models\Invoice;
	use yii\helpers\ArrayHelper;
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\GtdInvoice */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="gtd-invoice-form">

	<div class="row ">
		<div class="col-lg-4">
			<span class="text-bold"><?=Yii::t('app', 'GTD no')?>:</span>
			<span><?=$model->gtd->gtd_no?></span>
		</div>
		<div class="col-lg-3">
			<span class="text-bold"><?=Yii::t('app', 'GTD date')?>:</span>
			<span><?=$model->gtd->gtd_dt?></span>
		</div>
		<div class="col-lg-3">
			<span class="text-bold"><?=Yii::t('app', 'Post no')?>:</span>
			<span><?=$model->gtd->post_no?></span>
		</div>
	</div>

	<?php $form = ActiveForm::begin(); ?>
	<?=$form->field($model, 'gtd_id')->hiddenInput()->label(false)?>


	<div class="row ">
		<div class="col-lg-6 col-sm-12">
			<?
				$invoices = Invoice::find()->all();
				$invoice_items = ArrayHelper::map($invoices, 'id', 'invoice_no');
				$params = ['prompt' => '. . .', 'class' => 'form-control input-sm select2'];
				echo $form->field($model, 'invoice_id')->dropDownList($invoice_items, $params);
			?>
		</div>
		<div class="col-lg-6 col-sm-12">
			<?=$form->field($model, 'amount')->textInput(['maxlength' => true])?>
		</div>
	</div>
	<div class="form-group pull-right">
		<?=Html::a(Yii::t('app', 'btn-cancel'), ['gtd/view', 'id' => $model->gtd->id], ['class' => 'btn btn-default btn-sm'])?>
		<?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
