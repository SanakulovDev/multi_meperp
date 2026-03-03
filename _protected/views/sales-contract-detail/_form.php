<?php
	use yii\helpers\ArrayHelper;
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\ContractDetail */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="contract-detail-form">

	<?php $form = ActiveForm::begin(); ?>

	<div class="row">
		<div class="col-lg-6">

			<?=
				$form->field($model, 'sales_contract_id')->dropDownList(ArrayHelper::map(app\models\SalesContract::find()->all(), 'id', 'contractInfo'), [
					'class' => ' form-control select2',
					'prompt' => Yii::t('app', 'Select')
				]);
			?>
		</div>
		<div class="col-lg-6">
			<?=
				$form->field($model, 'delivery_term_id')->dropDownList(ArrayHelper::map(app\models\DeliveryTerm::find()->all(), 'id', 'name'), [
					'class' => ' form-control select2',
					'prompt' => Yii::t('app', 'Select')
				]);
			?>
		</div>

	</div>
	<div class="row">
		<div class="col-lg-6">


			<?=
				$form->field($model, 'part_id')->dropDownList(ArrayHelper::map(app\models\Part::find()->all(), 'id', 'partinfo'), [
					'class' => ' form-control select2',
					'prompt' => Yii::t('app', 'Select')
				]);
			?>
		</div>
		<div class="col-lg-2">

			<?=$form->field($model, 'price')->textInput(['maxlength' => true])?>
		</div>
    <div class="col-lg-2">

			<?= $form->field($model, 'vat')->textInput(['maxlength' => true]) ?>
		</div>
    <div class="col-lg-2">

			<?= $form->field($model, 'excise')->textInput(['maxlength' => true]) ?>
		</div>
	</div>
	<div class="form-group pull-right">
		<?=Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
		<?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
	</div>


</div>
</div>

<?php ActiveForm::end(); ?>

</div>
