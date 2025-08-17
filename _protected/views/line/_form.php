<?php
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\Line */
	/* @var $lines app\models\Line */
	/* @var $factories app\models\Factory */
	/* @var $form yii\widgets\ActiveForm */
	$validationUrl = ['validate'];
	if(!$model->isNewRecord){
		$validationUrl['id'] = $model->id;
	}
	$form = ActiveForm::begin([
		                          'id' => $model->formName(),
		                          'enableAjaxValidation' => true,
		                          'validateOnType' => false,
		                          'validationUrl' => $validationUrl,
		                          'options' => ['data-pjax' => true, 'class' => 'modalForm']
	                          ]);
?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6">
		<?=$form->field($model, 'line_name')->textInput(['maxlength' => true])?>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6">
		<?=$form->field($model, 'parent_id')->dropDownList($lines, ['prompt' => ' . . . '])?>
	</div>
</div>

<?=$form->field($model, 'description')->textInput(['maxlength' => true])?>

<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6">
		<?=$form->field($model, 'factory_id')->dropDownList($factories)?>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6">
		<?=$form->field($model, 'status')->dropDownList($model->statusList)?>
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
