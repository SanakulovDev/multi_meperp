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
		'action' => '/contract-detail/create',
		'class' => 'form-component'
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
		<!-- Sub source -->
		<div class="col-lg-2" style="display: none">
			<?=$form->field($model_detail, 'sub_source')->dropDownList($model_detail->subSourceList, [
					'class' => ' form-control select2',
					'value' => 3,
					'prompt' => Yii::t('app', 'Select')
				]);
			?>
		</div>
	</div>



	<div class="row">
		<!-- Цена для расчета -->
		<!-- <div class="col-lg-2">
			<?=$form->field($model_detail, 'is_primary_price')->dropDownList([ 0 => Yii::t('app', 'No'), 1 => Yii::t('app', 'Yes')])?>
		</div> -->
		<!-- ТН-ВЭД код -->
		<div class="col-lg-2" style="display: none">
			<?=$form->field($model_detail, 'cnfea')->textInput(['maxlength' => 10])?>
		</div>
	</div>


	<div class="form-group">
		<!-- <?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?> -->

		<button type="button" id="delete<?php echo($index) ?>" class="btn btn-danger btn-sm">Удалить</button>
	</div>
	

</div>

<?php ActiveForm::end(); ?>

</div>
<?php
	$add_item = <<< JS
	$('form#w' + $index).on('submit', function(e){
		e.preventDefault();		
		var datastring = $(this).serialize();
        $.ajax({
            type: "POST",
            url: "/contract-detail/create",
            data: datastring,
            success: function(data) {
				if (!isNaN(data)) {
					console.log('data', data)
					alert('Сохранено');
					$('#delete' + $index).show();
					$('#delete' + $index).attr('data-id', data);
				}
            }
        });
	});
	$('#delete' + $index).on('click', function(e) {
		e.preventDefault();
		let statusValue = $('#contract-status').val();
		let id = $id
		console.log(statusValue)
		if (statusValue > 1) {
			statusValue = statusValue - 1;
			$('form#w' + $index).remove();
			$('#contract-status').val(statusValue);

			var datastring = $('form#w0').serialize();

			$.ajax({
            type: "POST",
            url: `/contract/update?id=${id}&status=${statusValue}`,
            data: datastring,
            success: function(data) {
				if (!isNaN(data)) {
					alert('Удалено');
				}
            }
        	});
		}
		console.log()
	})
JS;
	$this->registerJs($add_item, yii\web\View::POS_END);
?>