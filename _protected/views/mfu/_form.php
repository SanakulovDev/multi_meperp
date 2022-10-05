<?php
	use yii\helpers\ArrayHelper;
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\Mfu */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="mfu-form">

	<?php $form = ActiveForm::begin(); ?>
	<div class="row">
		<div class="col-lg-4">
			<?=
				$form->field($model, 'part_id')->dropDownList(ArrayHelper::map(app\models\Part::find()->all(), 'id', 'partinfo'), [
					'class' => ' form-control select2',
					'prompt' => Yii::t('app', 'Select')
				]);
			?>
		</div>
		<div class="col-lg-4">
			<?=$form->field($model, 'capacity')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-lg-4">
			<?=
				$form->field($model, 'ship_mode_id')->dropDownList(ArrayHelper::map(app\models\ShipMode::find()->all(), 'id', 'name'), [
					'class' => ' form-control select2',
					'prompt' => Yii::t('app', 'Select')
				]);
			?>
		</div>
		<div class="col-lg-4">
			<?=$form->field($model, 'mfu_code')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-lg-4">
			<?=
				$form->field($model, 'contract_source_id')->dropDownList(ArrayHelper::map(app\models\ContractSource::find()->all(), 'id', 'name'), [
					'class' => ' form-control select2',
					'prompt' => Yii::t('app', 'Select')
				]);
			?>
		</div>
		<div class="col-lg-4">
			<?=$form->field($model, 'bank')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-lg-4">
			<?=
				$form->field($model, 'constraint')->dropDownList([1 => Yii::t('app', 'YES'), 0 => Yii::t('app', 'NO')], [
					'class' => ' form-control select2',
					'prompt' => Yii::t('app', 'Select')
				]);
			?>
		</div>
		<div class="col-lg-4">
			<?=
				$form->field($model, 'consolidation_type_id')->dropDownList(ArrayHelper::map(app\models\ConsolidationType::find()->all(), 'id', 'name'), [
					'class' => ' form-control select2',
					'prompt' => Yii::t('app', 'Select')
				]);
			?>
		</div>
		<div class="col-lg-4">
			<?=
				$form->field($model, 'moq')->textInput(['maxlength' => true])?>
		</div>
	</div>

	<div class="form-group">
		<?=Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
		<?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
