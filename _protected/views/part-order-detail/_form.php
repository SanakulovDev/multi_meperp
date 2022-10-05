<?php
	use app\models\Part;
	use kartik\datetime\DateTimePicker;
	use yii\helpers\ArrayHelper;
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\PartOrderDetail */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="part-order-detail-form">

	<?php $form = ActiveForm::begin(); ?>
	<?=$form->field($model, 'part_order_id')->hiddenInput()->label(false)?>
	<div class="row">

		<div class="col-lg-3 col-md-3 col-sm-3">
			<?
				$data = Part::find()
				            ->select(['id, concat(part_no,"-",part_name,"-",part_color) AS part_no'])
				            ->orderBy(['part_no' => SORT_ASC])
				            ->all();
				$items = ArrayHelper::map($data, 'id', 'part_no');
				$params = ['prompt' => '. . .', null, 'class' => 'form-control select2'];
				echo $form->field($model, 'part_id')->dropDownList($items, $params);
			?>
		</div>
		<div class="col-lg-3 col-md-3 col-sm-3">
			<?=$form->field($model, 'qty')->textInput()?>
		</div>
		<div class="col-lg-3 col-md-3 col-sm-3">
			<?=$form->field($model, 'exwrk_plan')->widget(DateTimePicker::classname(), [
				'pluginOptions' => [
					'language' => 'ru',
					'autoclose' => true,
					'format' => 'yyyy-mm-dd',
					'minView' => 'month',
					'maxView' => 'month',
				],
				'options' => ['autocomplete' => 'off']
			])?>
		</div>
		<div class="col-lg-3 col-md-3 col-sm-3">
			<?=$form->field($model, 'exwrk_actual')->widget(DateTimePicker::classname(), [
				'pluginOptions' => [
					'language' => 'ru',
					'autoclose' => true,
					'format' => 'yyyy-mm-dd',
					'minView' => 'month',
					'maxView' => 'month',
				],
				'options' => ['autocomplete' => 'off']
			])?>
		</div>
	</div>

	<div class="form-group">
		<?=Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success'])?>
	</div>
	<?php ActiveForm::end(); ?>

</div>
