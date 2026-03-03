<?php
	use kartik\datetime\DateTimePicker;
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\ProductionOrderDefectSearch */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="production-order-defect-search">

	<?php $form = ActiveForm::begin([
		                                'action' => ['index'],
		                                'method' => 'get',
	                                ]); ?>

	<div class="row">
		<div class="col-md-3">
			<?=
				$form->field($model, 'filter_from')->widget(DateTimePicker::classname(), [
					'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
					'layout' => '{picker}{input}{remove}',
					'removeButton' => ['position' => 'append'],
					'language' => 'ru',
					'pluginOptions' => [
						'autoclose' => true,
						'format' => 'yyyy-mm-dd',
						'startView' => 'month',
						'minView' => 'month',
						'maxView' => 'month',
					],
					'options' => [
						'autocomplete' => 'off',
						'placeholder' => 'С...',
						'class' => ' form-control input-sm'
					]
				])
				     ->label(false)
			?>
		</div>
		<div class="col-md-3">
			<?=
				$form->field($model, 'filter_to')->widget(DateTimePicker::classname(), [
					'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
					'layout' => '{picker}{input}{remove}',
					'removeButton' => ['position' => 'append'],
					'language' => 'ru',
					'pluginOptions' => [
						'autoclose' => true,
						'format' => 'yyyy-mm-dd',
						'startView' => 'month',
						'minView' => 'month',
						'maxView' => 'month',
					],
					'options' => [
						'autocomplete' => 'off',
						'placeholder' => 'До...',
						'class' => ' form-control input-sm'
					]
				])
				     ->label(false)
			?>
		</div>

		<div class="form-group">
			<?=Html::submitButton(Yii::t('app', 'btn-show'), ['class' => 'btn btn-primary btn-sm'])?>
		</div>
	</div>

	<?php ActiveForm::end(); ?>

</div>
