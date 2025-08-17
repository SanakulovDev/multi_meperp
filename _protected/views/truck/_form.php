<?php
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\Truck */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="truck-form">
	<?php $form = ActiveForm::begin(); ?>
	<div class="row ">
		<div class="col-md-6">
			<?=$form->field($model, 'model')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-md-6">
			<?=$form->field($model, 'number')->textInput(['maxlength' => true])?>
		</div>
	</div>
	<div class="form-group pull-right">
		<?=Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
		<?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
	</div>
	<?php ActiveForm::end(); ?>
</div>
