<?php
	use yii\helpers\ArrayHelper;
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\ContractDetail */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="contract-detail-form">

	<?php $form = ActiveForm::begin([
		'action' => '/contract-detail/create'
	]); ?>

	<div class="row">


		<div class="col-lg-3">
			
			<?if($model_detail->isNewRecord){?>
				<?=$form->field($model_detail, 'contract_id')->dropDownList(ArrayHelper::map(app\models\Contract::find()->all(), 'id', 'contractInfo'), [
						'class' => ' form-control select2',
						'value' => $id,
						'prompt' => Yii::t('app', 'Select')
				]);
				?>
			<?}else{?>
				<div class="form-group">
					<label class="control-label"><?=Yii::t('app', 'Contract')?></label>
					<label class="form-control" style="font-weight: normal;background-color: #f5f5f5;"><?=$model_detail->contract->contractInfo?></label>
				</div>
				<?=$form->field($model_detail, 'contract')->hiddenInput()->label(false);?>
			<?}?>
			
		</div>

		<div class="col-lg-2">
			
			<?if($model_detail->isNewRecord){?>
				<?=$form->field($model_detail, 'part_id')->dropDownList(ArrayHelper::map(app\models\Part::find()->all(), 'id', 'partinfo'), [
					'class' => ' form-control select2',
					'prompt' => Yii::t('app', 'Select')
				]);
				?>
			<?}else{?>
				<div class="form-group">
					<label class="control-label"><?=Yii::t('app', 'Part')?></label>
					<label class="form-control" style="font-weight: normal;background-color: #f5f5f5;"><?=$model_detail->part->partinfo?></label>
				</div>
				<?=$form->field($model_detail, 'part')->hiddenInput()->label(false);?>
			<?}?>

		</div>


		<div class="col-lg-2">
			<?=$form->field($model_detail, 'delivery_term_id')->dropDownList(ArrayHelper::map(app\models\DeliveryTerm::find()->all(), 'id', 'name'), [
					'class' => ' form-control select2',
					'value' => 9,
					'prompt' => Yii::t('app', 'Select')
				]);
			?>
		</div>
		<div class="col-lg-2">
			<?=$form->field($model_detail, 'price')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-lg-2">
			<?=$form->field($model_detail, 'lead_time')->textInput(['type'=>'number', 'value' => 7, 'maxlength' => true])?>
		</div>
		<div class="col-lg-1">
			<button type="button" class="btn btn-primary">+</button>
		</div>
		<!-- Sub source -->
		<!-- <div class="col-lg-2">
			<?=$form->field($model_detail, 'sub_source')->dropDownList($model_detail->subSourceList, [
					'class' => ' form-control select2',
					'prompt' => Yii::t('app', 'Select')
				]);
			?>
		</div> -->
	</div>



	<div class="row">
		<!-- Цена для расчета -->
		<!-- <div class="col-lg-2">
			<?=$form->field($model_detail, 'is_primary_price')->dropDownList([ 0 => Yii::t('app', 'No'), 1 => Yii::t('app', 'Yes')])?>
		</div> -->
		<!-- ТН-ВЭД код -->
		<!-- <div class="col-lg-2">
			<?=$form->field($model_detail, 'cnfea')->textInput(['maxlength' => 10])?>
		</div> -->
	</div>


	<div class="form-group">
		<?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
	</div>


</div>

<?php ActiveForm::end(); ?>

</div>
<?php
	$add_item = <<< JS
	$(document).ready(function() {
		console.log($id)
	});
JS;
	$this->registerJs($add_item, yii\web\View::POS_END);
?>