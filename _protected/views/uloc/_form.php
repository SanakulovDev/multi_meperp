<?php
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\Uloc */
	/* @var $lines app\models\Line */
	/* @var $form yii\widgets\ActiveForm */
	$validationUrl = ['validate'];
	if(!$model->isNewRecord){
		$validationUrl['id'] = $model->id;
	}
	$form = ActiveForm::begin([
		                          'id' => $model->formName(),
		                          'enableAjaxValidation' => true,
		                          'validateOnType' => false,
		                          'validationUrl' => $validationUrl,
		                          'options' => ['data-pjax' => true, 'class' => 'modalForm']
	                          ]);
?>

<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6">
		<?=$form->field($model, 'title')->textInput(['maxlength' => true])?>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6">
		<?=$form->field($model, 'line_id')->dropDownList($lines)?>
	</div>
</div>

<?=$form->field($model, 'description')->textInput(['maxlength' => true])?>

<div class="row">
	<div class="col-lg-3 col-md-3 col-sm-3">
		<?=$form->field($model, 'min_stock')->textInput(['maxlength' => true, 'type' => 'number'])?>
	</div>
	<div class="col-lg-3 col-md-3 col-sm-3">
		<?=$form->field($model, 'max_stock')->textInput(['maxlength' => true, 'type' => 'number'])?>
	</div>
	<div class="col-lg-3 col-md-3 col-sm-3">
		<?=$form->field($model, 'actual_stock')->textInput(['maxlength' => true, 'type' => 'number'])?>
	</div>
	<div class="col-lg-3 col-md-3 col-sm-3">
		<?=$form->field($model, 'status')->dropDownList($model->statusList)?>
	</div>
</div>


<?php if($model->isNewRecord == false): ?>
	<div class="">
		<table class="table table-bordered table-condensed">
			<tr>
				<th><?=Yii::t('app', 'Created by')?></th>
				<th><?=Yii::t('app', 'Created at')?></th>
				<th><?=Yii::t('app', 'Updated by')?></th>
				<th><?=Yii::t('app', 'Updated at')?></th>
			</tr>
			<tr>
				<td><?=$model->createdBy->fullname?></td>
				<td><?=$model->createdAtFormatted?></td>
				<td><?=$model->updatedBy->fullname?></td>
				<td><?=$model->updatedAtFormatted?></td>
			</tr>
		</table>
	</div>
<?php endif; ?>

<?php ActiveForm::end(); ?>
