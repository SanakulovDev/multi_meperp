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
		'action' => '/sales-contract-detail/create'
	]); ?>

	<div class="row">
		<div class="col-lg-6">

			<?=
				$form->field($model, 'sales_contract_id')->dropDownList(ArrayHelper::map(app\models\SalesContract::find()->all(), 'id', 'contractInfo'), [
					'class' => ' form-control select2',
					'value' => $customer,
					'prompt' => Yii::t('app', 'Select')
				]);
			?>
		</div>
		<div class="col-lg-6">
			<?=
				$form->field($model, 'delivery_term_id')->dropDownList(ArrayHelper::map(app\models\DeliveryTerm::find()->all(), 'id', 'name'), [
					'class' => ' form-control select2',
					'prompt' => Yii::t('app', 'Select')
				]);
			?>
		</div>

	</div>

	<div class="row">
		<div class="col-lg-6">
			<?=
				$form->field($model, 'part_id')->dropDownList(ArrayHelper::map(app\models\Part::find()->all(), 'id', 'partinfo'), [
					'class' => ' form-control select2',
					'prompt' => Yii::t('app', 'Select')
				]);
			?>
		</div>
		<div class="col-lg-2">
			<?=$form->field($model, 'price')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-lg-3">
			<?=$form->field($model, 'qty')->textInput(['maxlength' => true, 'type'=>'number'])?>
		</div>
	</div>
	
	<div class="row">
		<div class="col-lg-2">
			<?= $form->field($model, 'vat')->textInput(['maxlength' => true, 'value'=>12]) ?>
		</div>
	
		<div class="col-lg-2">
			<?= $form->field($model, 'excise')->textInput(['maxlength' => true, 'value'=>0]) ?>
		</div>
		<div class="col-lg-8">
			<div class="form-group pull-right">
				<!-- <?=Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
				<?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?> -->
				<button type="button" id="delete<?php echo($index) ?>" class="btn btn-danger btn-sm">Удалить</button>
			</div>
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
            url: "/sales-contract-detail/create?isAjax=1",
            data: datastring,
            success: function(data) {
				if (!isNaN(data)) {
					alert('Сохранено');
					$('#delete' + $index).show();
					$('#delete' + $index).attr('data-id', data);
				}
            }
        });
	});
	$('#delete' + $index).on('click', function(e) {
		e.preventDefault();
		// let statusValue = $('#contract-status').val();
		// let id = $id
		// console.log(statusValue)
		$('form#w' + $index).remove();
		// if (statusValue > 1) {
		// 	statusValue = statusValue - 1;
		// 	$('#contract-status').val(statusValue);

		// 	var datastring = $('form#w0').serialize();

		// 	$.ajax({
        //     type: "POST",
        //     url: `/contract/update?id=${id}&status=${statusValue}`,
        //     data: datastring,
        //     success: function(data) {
		// 		if (!isNaN(data)) {
		// 			alert('Удалено');
		// 		}
        //     }
        // 	});
		// }
	})
JS;
	$this->registerJs($add_item, yii\web\View::POS_END);
?>
