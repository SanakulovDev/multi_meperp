<?php
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\Driver */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="driver-form">

	<?php $form = ActiveForm::begin(); ?>
	<div class="row ">
		<div class="col-lg-3">
			<?=$form->field($model, 'last_name')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-lg-3">
			<?=$form->field($model, 'first_name')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-lg-3">
			<?=$form->field($model, 'middle_name')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-lg-3">
			<?=$form->field($model, 'emp_no')->textInput(['maxlength' => true])?>
		</div>
	</div>
	<div class="form-group pull-right">
		<?=Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
		<?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
	</div>
	<?php ActiveForm::end(); ?>

</div>
