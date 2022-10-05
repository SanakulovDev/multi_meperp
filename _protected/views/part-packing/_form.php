<?php
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\PartPacking */
	/* @var $form yii\widgets\ActiveForm */
	$validationUrl = ['validate'];
	if(!$model->isNewRecord){
		$validationUrl['id'] = $model->id;
	}
	$form = ActiveForm::begin(
		[
			'id' => $model->formName(),
			'enableAjaxValidation' => true,
			'validateOnType' => false,
			'validationUrl' => $validationUrl,
			'options' => ['data-pjax' => true, 'class' => 'modalForm']
		]);
?>

<div class="row">
	<div class="col-sm-4 col-md-4 col-lg-4">
		<?=$form->field($model, 'part_id')->dropDownList($parts, ['class' => 'form-control select2'])?>
	</div>
	<div class="col-sm-6 col-md-6 col-lg-6">
		<?=$form->field($model, 'supplier_id')->dropDownList($suppliers, ['prompt' => '. . .', 'class' => 'form-control select2'])?>
	</div>
	<div class="col-sm-2 col-md-2 col-lg-2">
		<?=$form->field($model, 'returnable')->dropDownList([1 => Yii::t('app', 'Y'), 0 => Yii::t('app', 'N')])?>
	</div>
</div>

<div class="row">
	<div class="col-sm-6 col-md-6 col-lg-6">
		<?=$form->field($model, 'pack_id')->dropDownList($packs, ['class' => 'form-control select2'])?>
	</div>
	<div class="col-sm-3 col-md-3 col-lg-3">
		<?=$form->field($model, 'piece_weight')->textInput(['maxlength' => true, 'type' => 'number'])?>
	</div>
	<div class="col-sm-3 col-md-3 col-lg-3">
		<?=$form->field($model, 'pack_qty')->textInput(['maxlength' => true, 'type' => 'number'])?>
	</div>
</div>

<?php if(!$model->isNewRecord): ?>
	<div class="row">
		<div class="col-sm-6 col-md-6 col-lg-6">
			<span class="form-control">
				<?=$model->getAttributeLabel('created_by').' '.$model->createdBy->fullname.' '.$model->createdAtFormatted?>
			</span>
		</div>
		<div class="col-sm-6 col-md-6 col-lg-6">
			<span class="form-control">
				<?=$model->getAttributeLabel('updated_by').' '.$model->updatedBy->fullname.' '.$model->updatedAtFormatted?>
			</span>
		</div>
	</div>
<?php endif ?>
<?php ActiveForm::end(); ?>
