<?php
	use yii\helpers\ArrayHelper;
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\ApiDetail */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="api-detail-form">

	<?php $form = ActiveForm::begin(); ?>
	<?=$form->field($model, 'api_id')->hiddenInput()->label(false)?>
	<div class="row">
		<div class="col-lg-4">


			<div class="form-group">
				<label class="control-label" for="apidetail-api_id"><?=$model->getAttributeLabel('api_id')?></label>
				<input type="text" class="form-control" value="<?=$model->api->invinfo?>" disabled="1">
				<div class="help-block"></div>
			</div>
		</div>
		<div class="col-lg-4">
			<?=
				$form->field($model, 'part_id')->dropDownList(ArrayHelper::map(app\models\Part::find()->all(), 'id', 'partinfo'), [
					'class' => ' form-control select2',
					'prompt' => Yii::t('app', 'Select')
				]);
			?>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-4">
			<?=$form->field($model, 'inventory_qty')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-lg-4">
			<?=$form->field($model, 'stock_qty')->textInput(['maxlength' => true])?>
		</div>
	</div>


	<div class="form-group">
		<?=Html::a(Yii::t('app', 'btn-cancel'), ['inventory/update', 'id' => $model->api_id], ['class' => 'btn btn-default btn-sm'])?>
		<?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-primary btn-sm'])?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
