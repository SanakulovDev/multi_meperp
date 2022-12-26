<?php
	use kartik\datetime\DateTimePicker;
	use yii\helpers\ArrayHelper;
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\Contract */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="contract-form">

	<?php $form = ActiveForm::begin(); ?>

	<div class="row">

		<div class="col-lg-4">
			<?=
				$form->field($model, 'supplier_id')->dropDownList(ArrayHelper::map(app\models\Supplier::find()->all(), 'id', 'name'), [
					'class' => ' form-control select2',
					'disabled' => $isUpdating ? true : false,
					'prompt' => Yii::t('app', 'Select')
				]);
			?>
		</div>
		<div class="col-lg-4">
			<?=$form->field($model, 'contract_no')->textInput(['maxlength' => true, 'disabled' => $isUpdating ? true : false,])?>
		</div>
		<div class="col-lg-4">
			<?=$form->field($model, 'contract_date')->widget(DateTimePicker::classname(), [
				'pluginOptions' => [
					'language' => 'ru',
					'autoclose' => true,
					'format' => 'yyyy-mm-dd',
					'minView' => 'month',
					'maxView' => 'month',
					'disabled' => $isUpdating ? true : false,
				],
				'options' => [
					'value' => $update ? $model->contract_date :  date('Y-m-d'),
					'autocomplete' => 'off',
					'disabled' => $isUpdating ? true : false,
				]
			])?>
		</div>
		<div class="col-lg-4">
			<?=$form->field($model, 'expiry_date')->widget(DateTimePicker::classname(), [
				'pluginOptions' => [
					'language' => 'ru',
					'autoclose' => true,
					'format' => 'yyyy-mm-dd',
					'minView' => 'month',
					'maxView' => 'month',
					'disabled' => $isUpdating ? true : false,
				],
				'options' => [
					'disabled' => $isUpdating ? true : false,
					'autocomplete' => 'off'
				]
			])?>
		</div>
		<div class="col-lg-4 d-none" style="display: none">
			<?=
				$form->field($model, 'buyer_id')->dropDownList(ArrayHelper::map(\app\models\User::find()->joinWith('role')->where(['item_name' => 'buyer'])->all(), 'id', 'fullname'), [
					'class' => ' form-control select2',
					'value' => $isUpdating ? $model->buyer_id : '4',
					'disabled' => $isUpdating ? true : false,
					'prompt' => Yii::t('app', 'Select')
				]);
			?>
		</div>
		<div class="col-lg-4">
			<?=
				$form->field($model, 'payment_term_id')->dropDownList(ArrayHelper::map(app\models\PaymentTerm::find()->all(), 'id', 'name'), [
					'class' => ' form-control select2',
					'selected' => 2,
					'value' => $isUpdating ? $model->payment_term_id : '2',
					'disabled' => $isUpdating ? true : false,
					'prompt' => Yii::t('app', 'Select')
				]);
			?>
		</div>

		<div class="col-lg-4">
			<?=$form->field($model, 'contract_amount')->textInput(['maxlength' => true, 'disabled' => $isUpdating ? true : false,])?>
		</div>
		<div class="col-lg-4">
			<?=
				$form->field($model, 'contract_subject_id')->dropDownList(ArrayHelper::map(app\models\ContractSubject::find()->all(), 'id', 'name'), [
					'class' => ' form-control select2',
					'disabled' => $isUpdating ? true : false,
					'prompt' => Yii::t('app', 'Select')
				]);
			?>
		</div>
		<div class="col-lg-3">
			<?=
				$form->field($model, 'currency_id')->dropDownList(ArrayHelper::map(app\models\Currency::find()->all(), 'id', 'name'), [
					'class' => ' form-control select2',
					'disabled' => $isUpdating ? true : false,
					'prompt' => Yii::t('app', 'Select')
				]);
			?>
		</div>
		<div class="col-lg-4">
			<?=
				$form->field($model, 'contract_source_id')->dropDownList(ArrayHelper::map(app\models\ContractSource::find()->all(), 'id', 'name'), [
					'class' => ' form-control select2',
					'value' => $isUpdating ? $model->contract_source_id : '2',
					'disabled' => $isUpdating ? true : false,
					'prompt' => Yii::t('app', 'Select')
				]);
			?>
		</div>
		<div class="col-lg-1">
			<?=$form->field($model, 'status')->textInput(['maxlength' => 10, 'type' => 'number', 'required' => true, 'value' => $count ? $model->status : 0, 'disabled' => $count ? true : false])?>
		</div>
		<!-- <div class="col-md-4 col-sm-4 col-lg-4">
			<?=$form->field($model, 'status')->dropDownList($model->statusList, [
				'disabled' => ($isUpdating || $count) ? true : false,
				'type' => 'number',
				'maxlength' => 10
			])?>
		</div> -->


	</div>

	<div class="form-group">
		<?=Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
		<?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm', 'id' => 'contract-form', 'type'=> 'button'])?>
	</div>
	<?php ActiveForm::end(); ?>

</div>
<?php
	$model = <<< JS
	$(document).ready(function() {
		$('#contract-status').on('input', () => {
			const value = $('#contract-status').val()
			if (value && value > 5) {
				$('#contract-status').val(5)
			}
		})
	});
JS;
	$this->registerJs($model, yii\web\View::POS_END);
?>
