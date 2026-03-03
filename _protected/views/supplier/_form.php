<?php
	use app\models\CountryCode;
	use yii\helpers\ArrayHelper;
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\Supplier */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="supplier-form">

	<?php $form = ActiveForm::begin(); ?>
	<div class="row">
		<div class="col-lg-6 col-sm-6">
			<?=$form->field($model, 'name')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-lg-3 col-sm-3">
			<?=$form->field($model, 'duns')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-lg-3 col-sm-3">
			<?=$form->field($model, 'alias')->textInput(['maxlength' => true])?>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-6 col-sm-6">
			<?=$form->field($model, 'country_code_id')
			        ->dropDownList(ArrayHelper::map(CountryCode::find()->all(), 'id', 'name'), ['class' => 'form-control select2'])?>
		</div>
		<div class="col-lg-3 col-sm-3">
			<?=$form->field($model, 'city')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-lg-3 col-sm-3">
			<?=$form->field($model, 'postal')->textInput(['maxlength' => true])?>
		</div>
	</div>

	<?=$form->field($model, 'address')->textInput(['maxlength' => true])?>
	<div class="row">
		<div class="col-lg-5 col-sm-5">
			<?=$form->field($model, 'contact_name')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-lg-4 col-sm-4">
			<?=$form->field($model, 'contact_position')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-lg-3 col-sm-3">
			<?= $form->field($model, 'transit_time')->textInput(['type'=>'number', 'maxlength' => true])?>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-4 col-sm-4">
			<?=$form->field($model, 'contact_email')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-lg-4 col-sm-4">
			<?=$form->field($model, 'contact_phone')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-lg-4 col-sm-4">
			<?=$form->field($model, 'contact_cellular')->textInput(['maxlength' => true])?>
		</div>
	</div>

	<div class="form-group">
		<?=Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
		<?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
