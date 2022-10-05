<?php
	use kartik\datetime\DateTimePicker;
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\ReceivingPerson */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="receiving-person-form">

	<?php $form = ActiveForm::begin(); ?>

	<div class="row">
		<div class="col-sm-9 col-md-9 col-lg-9">
			<?=$form->field($model, 'fullname')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-sm-3 col-md-3 col-lg-3">
			<?=$form->field($model, 'status')->dropDownList($model->statusList)?>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6 col-md-6 col-lg-6">
			<?=$form->field($model, 'doc_number')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-sm-6 col-md-6 col-lg-6">
			<?=$form->field($model, 'doc_date')->widget(DateTimePicker::classname(), [
				'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
				'layout' => '{picker}{input}{remove}',
				'removeButton' => ['position' => 'append'],
				'language' => Yii::$app->language,
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
	</div>

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
