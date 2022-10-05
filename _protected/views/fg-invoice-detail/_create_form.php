<?php
	use kartik\select2\Select2;
	use yii\helpers\Html;
	use yii\helpers\Url;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\FgInvoiceDetail */
	/* @var $form yii\widgets\ActiveForm */
	/** @var TYPE_NAME $part_items */
	/** @var TYPE_NAME $fg_inv_id */
?>

<div class="fg-invoice-detail-form">

	<?=Html::hiddenInput('fg_inv_id', $fg_inv_id, $options = ['id'=>'fg_inv_id'])?>
	<?=Html::label('Parts')?>
	<?=
		Select2::widget(
		[
			'id' => 'part',
			'name' => 'part',
			'value' => '',
			'data' => $part_items,
			'maintainOrder' => true,
			'options' => ['placeholder' => '. . .', 'class' => 'form-control select2'],
			'pluginOptions' => ['tags' => true, 'maximumInputLength' => 10]
		]);
	?>
	<?php $form = ActiveForm::begin(); ?>
	<div class="row">
		<div class="col-lg-5">
			<?=$form->field($model, 'part_no')->hiddenInput()?>
			<?=Html::textInput('view-part_no', null, ['id' =>'view-part_no','disabled' => true])?>
		</div>
		<div class="col-lg-7">
			<?=$form->field($model, 'part_name')->hiddenInput()?>
			<?=Html::textInput('view-part_name', null, ['id' =>'view-part_name','disabled' => true, 'style'=>'width:100%'])?>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-4">
			<?=$form->field($model, 'qty')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-lg-4">
			<?=$form->field($model, 'price')->hiddenInput()?>
			<?=Html::textInput('view-price', null, ['id' =>'view-price','disabled' => true])?>
		</div>
		<div class="col-lg-4">
			<?=$form->field($model, 'unit_id')->hiddenInput()?>
			<?=Html::textInput('view-unit_nm', null, ['id' =>'view-unit_nm','disabled' => true])?>
		</div>
	</div>

	<input type="hidden" name="<?=Yii::$app->request->csrfParam;?>" value="<?=Yii::$app->request->getCsrfToken();?>"/>
	<div class="form-group">
		<!--		--><? //=Html::a(Yii::t('app', 'btn-cancel'), ['fg-invoice/update', 'id' => $model->fgInvoice->id], ['class' => 'btn btn-default btn-sm'])?>
		<?=Html::a(Yii::t('app', 'btn-cancel'), ['fg-invoice/index'], ['class' => 'btn btn-default btn-sm'])?>
		<?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
	</div>

	<?php ActiveForm::end(); ?>

</div>

<?
	$url = Url::to(['sales-contract/list-by-sales-supplier'], true);
	$add_item = <<< JS
	$(document).ready(function() {	  
		
	 $('#part').on('change', function (){	
						$.ajax({
							url: "part-data",
							type: "post",
							data: { 
								partid: this.value,
								fg_inv_id: $('#fg_inv_id').val(),
							},
							success: function(response) {
								$("#fginvoicedetail-part_no").val(response.part_no);
								$("#fginvoicedetail-part_name").val(response.part_name);
								$("#fginvoicedetail-price").val(response.price);
								$("#fginvoicedetail-unit_id").val(response.unit_id);
								
								$("#view-part_no").val(response.part_no);
								$("#view-part_name").val(response.part_name);
								$("#view-price").val(response.price);
								$("#view-unit_nm").val(response.unit_nm);
							},
							error: function(xhr) {
								console.log(xhr)
							}
						});
				});
	
	});
JS;
	$this->registerJs($add_item, yii\web\View::POS_END);
?>