<?php
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\CustomerType */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="customer-type-form">

	<?php $form = ActiveForm::begin(); ?>

	<div class="row">
		<div class="col-sm-6 col-md-6 col-lg-6">
			<?=$form->field($model, 'name')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-sm-6 col-md-6 col-lg-6">
			<?=$form->field($model, 'status')->dropDownList($model->statusList)?>
		</div>
	</div>


	<?=$form->field($model, 'description')->textInput(['maxlength' => true])?>

	<div class="form-group">
		<?=Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
		<?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
	</div>

	<?php ActiveForm::end(); ?>
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

</div>
