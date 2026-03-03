<?php
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\DocumentType */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="document-type-form">

	<?php $form = ActiveForm::begin(); ?>

	<?=$form->field($model, 'code')->textInput(['maxlength' => true])?>
	<?=$form->field($model, 'name')->textInput(['maxlength' => true])?>
	<?=$form->field($model, 'description')->textarea(['rows' => 6])?>

	<div class="form-group">
		<?=Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
		<?=Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success'])?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
