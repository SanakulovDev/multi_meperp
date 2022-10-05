<?php
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\ProductionOrder */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="production-order-form">

	<?php $form = ActiveForm::begin(); ?>

	<div class="row">
		<div class="col-lg-4 col-sm-4">
			<?=$form->field($model, 'part_id')->dropDownList($parts, ['class' => 'form-control select2'])?>
		</div>
		<div class="col-lg-3 col-sm-3">
			<?=$form->field($model, 'qty')->textInput()?>
		</div>
	</div>


	<div class="form-group">
		<?=Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
		<?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
