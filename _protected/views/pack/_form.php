<?php
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\Pack */
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
  <div class="col-sm-4 col-md-4 col-lg-4">
    <?=$form->field($model, 'code')->textInput(['maxlength' => true])?>
  </div>
  <div class="col-sm-8 col-md-8 col-lg-8">
    <?=$form->field($model, 'description')->textInput(['maxlength' => true])?>
  </div>
</div>

<div class="row">
  <div class="col-sm-4 col-md-4 col-lg-4">
    <?=$form->field($model, 'level')->textInput(['maxlength' => true, 'type' => 'number'])?>
  </div>
	<div class="col-sm-8 col-md-8 col-lg-8">
		<?=$form->field($model, 'construction')->textInput(['maxlength' => true])?>
	</div>
</div>
<div class="row">
	<div class="col-sm-4 col-md-4 col-lg-4">
		<?=$form->field($model, 'length')->textInput(['maxlength' => true, 'type' => 'number'])?>
	</div>
	<div class="col-sm-4 col-md-4 col-lg-4">
		<?=$form->field($model, 'width')->textInput(['maxlength' => true, 'type' => 'number'])?>
	</div>
	<div class="col-sm-4 col-md-4 col-lg-4">
		<?=$form->field($model, 'height')->textInput(['maxlength' => true, 'type' => 'number'])?>
	</div>
</div>
<div class="row">
	<div class="col-sm-4 col-md-4 col-lg-4">
		<?=$form->field($model, 'weight')->textInput(['maxlength' => true, 'type' => 'number'])?>
	</div>
  <div class="col-sm-4 col-md-4 col-lg-4">
    <?=$form->field($model, 'thickness')->textInput(['maxlength' => true, 'type' => 'number'])?>
  </div>
  <div class="col-sm-4 col-md-4 col-lg-4">
    <?=$form->field($model, 'quantity')->textInput(['maxlength' => true, 'type' => 'number'])?>
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
