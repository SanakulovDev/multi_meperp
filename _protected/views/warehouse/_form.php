<?php
	use app\models\WarehouseReportGroup;
	use yii\helpers\ArrayHelper;
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\Warehouse */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="warehouse-form">

	<?php $form = ActiveForm::begin(); ?>

	<div class="row">
		<div class="col-sm-3">
			<?=$form->field($model, 'name')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-sm-6">
			<?=$form->field($model, 'description')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-sm-3">
			<?=
				$form->field($model, 'warehouse_report_group_id')->dropDownList(ArrayHelper::map(WarehouseReportGroup::find()->all(), 'id', 'title'), [
					'class' => ' form-control select2',
					'prompt' => Yii::t('app', 'Select')
				]);
			?>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-3">
			<?=$form->field($model, 'warehouse_type')->dropDownList($model->typeListNames)?>
		</div>
		<div class="col-sm-3">
			<?=$form->field($model, 'status')->dropDownList($model->statusList)?>
		</div>
		<div class="col-sm-6">
			<?=
				$form->field($model, 'supplier_id')->dropDownList(ArrayHelper::map(app\models\Supplier::find()->all(), 'id', 'name'), [
					'class' => ' form-control select2',
					'prompt' => Yii::t('app', 'Select')
				]);
			?>
		</div>
	</div>
</div>

<div class="form-group">
	<?=Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
	<?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
</div>

<?php ActiveForm::end(); ?>

</div>
