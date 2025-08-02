<?php
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\Contact */
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
			<?=$form->field($model, 'name')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-sm-4 col-md-4 col-lg-4">
			<?=$form->field($model, 'department')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-sm-4 col-md-4 col-lg-4">
			<?=$form->field($model, 'team')->textInput(['maxlength' => true])?>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-4 col-md-4 col-lg-4">
			<?=$form->field($model, 'office_phone')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-sm-4 col-md-4 col-lg-4">
			<?=$form->field($model, 'mobile_phone')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-sm-4 col-md-4 col-lg-4">
			<?=$form->field($model, 'email')->textInput(['maxlength' => true])?>
		</div>
	</div>

<?=$form->field($model, 'functionality')->textInput(['maxlength' => true])?>
<?=$form->field($model, 'responsibility')->textInput(['maxlength' => true])?>

	<div class="row">
		<div class="col-sm-6 col-md-6 col-lg-6">
			<?=$form->field($model, 'mrp_code')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-sm-6 col-md-6 col-lg-6">
			<?=$form->field($model, 'mfu_code')->textInput(['maxlength' => true])?>
		</div>
	</div>

<?php ActiveForm::end(); ?>