<?php
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\WarehouseReportGroup */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="warehouse-report-group-form">

	<?php $form = ActiveForm::begin(); ?>
	<?=$form->field($model, 'title')->textInput(['maxlength' => true])?>
	<?=$form->field($model, 'description')->textInput(['maxlength' => true])?>
	<?=$form->field($model, 'sort_order')->textInput(['maxlength' => true])?>
	<div class="form-group">
		<?=Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
		<?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
	</div>
	<?php ActiveForm::end(); ?>

	

</div>
