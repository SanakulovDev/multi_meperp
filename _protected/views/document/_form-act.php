<?php
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\Document */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="document-form">

	<?php $form = ActiveForm::begin(); ?>
	<div class="row">

		<? if($isNewRecord ?? null){ ?>
			<div class="col-lg-3">
				<?=$form->field($model, 'adj')->dropDownList([0 => Yii::t('app', 'Issue'), 1 => Yii::t('app', 'Receipt')])?>
			</div>
			<div class="col-lg-3">
				<?=$form->field($model, 'adj_wh_id')->dropDownList($user_warehouses, ['prompt' => Yii::t('app', 'Select...'), 'class' => 'form-control select2'])?>
			</div>
		<? }else{ ?>
			<div class="col-lg-3">
				<div class="form-group">
					<label class="control-label"><?=Yii::t('app', 'Adjustment')?></label>
					<label class="form-control" style="font-weight: normal;background-color: #f5f5f5;"><?=$model->adjName?></label>
				</div>
				<?=$form->field($model, 'adj')->hiddenInput(['value' => $model->adjStatus])->label(false);?>
			</div>

			<div class="col-lg-3">
				<div class="form-group">
					<label class="control-label"><?=Yii::t('app', 'Warehouse')?></label>
					<label class="form-control" style="font-weight: normal;background-color: #f5f5f5;"><?=$model->adjWhName?></label>
				</div>
				<?=$form->field($model, 'adj_wh_id')->hiddenInput(['value' => $model->adjWhId])->label(false);?>
			</div>
		<? } ?>


		<div class="col-lg-3">
			<div class="form-group">
				<label class="control-label"><?=Yii::t('app', 'Document date')?></label>
				<label class="form-control" style="font-weight: normal;background-color: #f5f5f5;"><?=$model->docdate?></label>
			</div>
		</div>

	</div>
	<div class="row">
		<div class="col-lg-12">
			<?=$form->field($model, 'comment')->textInput();?>
		</div>
	</div>

	<?=$this->render('__details', [
		'errorlist' => $errorlist ?? null,
		'model' => $model,
		'items' => $items,
		'modelItems' => $modelItems,
	])?>


	<div class="form-group pull-right">
		<?=Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
		<?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
