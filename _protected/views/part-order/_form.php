<?php
	use app\models\Contract;
	use app\models\DeliveryTerm;
	use kartik\datetime\DateTimePicker;
	use yii\helpers\ArrayHelper;
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\PartOrder */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="part-order-form">
  <? if(isset($errMsg)) {
    echo '<div class="alert-danger alert fade in">'.$errMsg.'</div>';
  }?>

	<?php $form = ActiveForm::begin(); ?>
	<div class="row">
		<div class="col-md-6 col-sm-6 col-lg-6">
			<?=$form->field($model, 'order_no')->textInput([
				'maxlength' => true,
				'value' => $contract_model->contract_no
			])?>
		</div>
		<div class="col-md-6 col-sm-6 col-lg-6">
			<?
				$data = Contract::find()->where('status=1')->orderBy(['contract_no' => SORT_ASC])->all();
				$items = ArrayHelper::map($data, 'id', 'contract_no');
				$params = ['prompt' => '. . .', null, 'class' => 'form-control select2'];
				echo $form->field($model, 'contract_id')->dropDownList($items, [
					'value' => $contract_model->id
				]);
			?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-3 col-sm-3 col-lg-3">
			<?=$form->field($model, 'iss_dt')->widget(DateTimePicker::classname(), [
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
					'placeholder' => 'YYYY-MM-DD',
					'class' => ' form-control'
				]
			])->label(Yii::t('app', 'Issued date'));
			?>
		</div>
		<div class="col-md-3 col-sm-3 col-lg-3">
			<?=$form->field($model, 'mr_dt')->widget(DateTimePicker::classname(), [
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
					'placeholder' => 'YYYY-MM-DD',
					'class' => ' form-control'
				]
			])->label(Yii::t('app', 'Material required date'));
			?>
		</div>
		<!-- <div class="col-md-2 col-sm-2 col-lg-2">
			<?=$form->field($model, 'for_month')->dropDownList($model->getMonths(),['prompt' => '...'])?>
		</div> -->

		<!-- <div class="col-md-2 col-sm-2 col-lg-2">
			<?=$form->field($model, 'order_type')->dropDownList($model->orderTypeList)?>
		</div> -->
		<div class="col-md-2 col-sm-2 col-lg-2">
			<?
				$data = DeliveryTerm::find()->orderBy(['name' => SORT_ASC])->all();
				$items = ArrayHelper::map($data, 'id', 'name');
				$params = ['prompt' => '. . .', null, 'class' => 'form-control select2'];
				echo $form->field($model, 'delivery_term_id')->dropDownList($items, $params);
			?>
		</div>
	</div>
	<div class="form-group pull-right">
		<?=Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
		<?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
	</div>
	<?php ActiveForm::end(); ?>
</div>
