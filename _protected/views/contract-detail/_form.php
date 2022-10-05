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
			
			<?if($model->isNewRecord){?>
				<?=$form->field($model, 'contract_id')->dropDownList(ArrayHelper::map(app\models\Contract::find()->all(), 'id', 'contractInfo'), [
						'class' => ' form-control select2',
						'prompt' => Yii::t('app', 'Select')
				]);
				?>
			<?}else{?>
				<div class="form-group">
					<label class="control-label"><?=Yii::t('app', 'Contract')?></label>
					<label class="form-control" style="font-weight: normal;background-color: #f5f5f5;"><?=$model->contract->contractInfo?></label>
				</div>
				<?=$form->field($model, 'contract')->hiddenInput()->label(false);?>
			<?}?>
			
		</div>

		<div class="col-lg-2">
			
			<?if($model->isNewRecord){?>
				<?=$form->field($model, 'part_id')->dropDownList(ArrayHelper::map(app\models\Part::find()->all(), 'id', 'partinfo'), [
					'class' => ' form-control select2',
					'prompt' => Yii::t('app', 'Select')
				]);
				?>
			<?}else{?>
				<div class="form-group">
					<label class="control-label"><?=Yii::t('app', 'Part')?></label>
					<label class="form-control" style="font-weight: normal;background-color: #f5f5f5;"><?=$model->part->partinfo?></label>
				</div>
				<?=$form->field($model, 'part')->hiddenInput()->label(false);?>
			<?}?>

		</div>


		<div class="col-lg-2">
			<?=$form->field($model, 'delivery_term_id')->dropDownList(ArrayHelper::map(app\models\DeliveryTerm::find()->all(), 'id', 'name'), [
					'class' => ' form-control select2',
					'prompt' => Yii::t('app', 'Select')
				]);
			?>
		</div>
		<div class="col-lg-2">
			<?=$form->field($model, 'sub_source')->dropDownList($model->subSourceList, [
					'class' => ' form-control select2',
					'prompt' => Yii::t('app', 'Select')
				]);
			?>
		</div>
	</div>



	<div class="row">
		<div class="col-lg-2">
			<?=$form->field($model, 'price')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-lg-2">
			<?=$form->field($model, 'is_primary_price')->dropDownList([ 0 => Yii::t('app', 'No'), 1 => Yii::t('app', 'Yes')])?>
		</div>
		<div class="col-lg-2">
			<?=$form->field($model, 'cnfea')->textInput(['maxlength' => 10])?>
		</div>
		<div class="col-lg-2">
			<?=$form->field($model, 'lead_time')->textInput(['type'=>'number', 'maxlength' => true])?>
		</div>

	</div>


	<div class="form-group">
		<?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
	</div>


</div>

<?php ActiveForm::end(); ?>

</div>
