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
				$form->field($model, 'customer_id')->dropDownList(ArrayHelper::map(app\models\Customer::find()->all(), 'id', 'name'), [
					'class' => ' form-control select2',
					'prompt' => Yii::t('app', 'Select')
				]);
			?>
		</div>
		<div class="col-lg-4">
			<?=$form->field($model, 'contract_no')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-lg-4">
			<?=$form->field($model, 'contract_date')->widget(DateTimePicker::classname(), [
				'pluginOptions' => [
					'language' => 'ru',
					'autoclose' => true,
					'format' => 'yyyy-mm-dd',
					'minView' => 'month',
					'maxView' => 'month',
				],
				'options' => [
					'value' => $update ? $model->contract_date : date('Y-m-d'),
					'autocomplete' => 'off'
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
				],
				'options' => [
					'value' => $update ? $model->expiry_date : date('Y-m-d', strtotime("+12 months ". date('Y-m-d'))),
					'autocomplete' => 'off'
				]
			])?>
		</div>
		<div class="col-lg-4">
			<?=
				$form->field($model, 'seller_id')->dropDownList(ArrayHelper::map(\app\models\User::find()->joinWith('role')->where(['item_name' => 'shipper'])->all(), 'id', 'fullname'), [
					'class' => ' form-control select2',
					'value' =>  $update ? $model->seller_id : 11,
					'prompt' => Yii::t('app', 'Select')
				]);
			?>
		</div>
		<div class="col-lg-4">
			<?=
				$form->field($model, 'payment_term_id')->dropDownList(ArrayHelper::map(app\models\PaymentTerm::find()->all(), 'id', 'name'), [
					'class' => ' form-control select2',
					'prompt' => Yii::t('app', 'Select'),
					'value' => $update ? $model->payment_term_id : 2
				]);
			?>
		</div>

		<div class="col-lg-4">
			<?=$form->field($model, 'contract_amount')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-lg-4">
			<?=
				$form->field($model, 'contract_subject_id')->dropDownList(ArrayHelper::map(app\models\ContractSubject::find()->all(), 'id', 'name'), [
					'class' => ' form-control select2',
					'prompt' => Yii::t('app', 'Select')
				]);
			?>
		</div>
		<div class="col-md-4 col-sm-4 col-lg-4">
			<!-- <?=$form->field($model, 'status')->dropDownList($model->statusList)?> -->
			<?=$form->field($model, 'status')->textInput(['maxlength' => true, 'required' => true, 'disabled' => $model->status ? true : false ])?>
		</div>
		<div class="col-lg-4">
			<?=
				$form->field($model, 'currency_id')->dropDownList(ArrayHelper::map(app\models\Currency::find()->all(), 'id', 'code'), [
					'class' => ' form-control select2',
					'prompt' => Yii::t('app', 'Select')
				]);
			?>
		</div>

		<div class="col-lg-8">
			<div class="form-group pull-right">
				<?=Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
				<?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
			</div>
		</div>


	</div>

	<?php ActiveForm::end(); ?>

</div>

<?php
	$model = <<< JS
	$(document).ready(function() {
		$('#salescontract-status').on('input', () => {
			const value = $('#salescontract-status').val()
			if (value && value >10) {
				$('#salescontract-status').val(10)
			}
		})
	});
JS;
	$this->registerJs($model, yii\web\View::POS_END);
?>
