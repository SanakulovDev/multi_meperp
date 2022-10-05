<?php
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\FgInvoiceDetail */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="fg-invoice-detail-form">

	<?php $form = ActiveForm::begin(); ?>

	<div class="row">
		<div class="col-lg-5">
			<?=$form->field($model, 'part_no')->hiddenInput()->label(false)?>
			<?=Html::label('Part no')?><p><?=$model->part_no?></p>
		</div>
		<div class="col-lg-7">
			<?=$form->field($model, 'part_name')->hiddenInput()->label(false)?>
			<?=Html::label('Part name')?><p><?=$model->part_name?></p>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-4">
      <?=$form->field($model, 'qty')->textInput(
			  [
			    'type'=>'number',
          'min' => 1,
          'step' => "any",
			    'maxlength' => true,
          'class' => 'chng-val form-control input-sm',
          'style' => 'width:150px'
        ]
      )?>
		</div>
		<div class="col-lg-4">
			<?=$form->field($model, 'price')->hiddenInput()->label(false)?>
			<?=Html::label('Part name')?><p><?=$model->price?></p>
		</div>
		<div class="col-lg-4">
			<?=$form->field($model, 'unit_id')->hiddenInput()->label(false)?>
			<?=Html::label('Part name')?><p><?=$model->unit->unit_value?></p>
		</div>
	</div>

	<?php if($model->isNewRecord == false): ?>
		<div class="">
			<table class="table table-bordered table-condensed">
				<tr>
					<th><?=Yii::t('app', 'Created by')?></th>
					<th><?=Yii::t('app', 'Updated by')?></th>
				</tr>
				<tr>
					<td><?=$model->createdBy->username?><br><?=date('d.m.Y (H:i:s)', $model->created_at)?></td>
					<td>
						<? if(!empty($model->updatedBy)){
							echo $model->updatedBy->username."<br>".date('d.m.Y (H:i:s)', $model->updated_at);
						}?>
					</td>
				</tr>
			</table>
		</div>
	<?php endif; ?>



	<input type="hidden" name="<?=Yii::$app->request->csrfParam;?>" value="<?=Yii::$app->request->getCsrfToken();?>"/>
	<div class="form-group">
		<?=Html::a(Yii::t('app', 'btn-cancel'), ['fg-invoice/update', 'id' => $model->fgInvoice->id], ['class' => 'btn btn-default btn-sm'])?>
		<?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
