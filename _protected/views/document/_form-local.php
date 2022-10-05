<?php
	use yii\helpers\Html;
	use yii\helpers\Url;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\Document */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="document-form">
	<?php $form = ActiveForm::begin(); ?>
	<div class="row">

		<? if($isNewRecord ?? null){ ?>
			<div class="col-lg-4">
				<div class="form-group">
					<label class="control-label"><?=Yii::t('app', 'From supplier warehouse')?></label>
					<?=$form->field($model, 'from_warehouse_id')->dropDownList(yii\helpers\ArrayHelper::map(app\models\Warehouse::find()->where(['warehouse_type' => 3])->all(), 'id', 'name'),
					                                                           [
						                                                           'prompt' => Yii::t('app', 'Select...'),
						                                                           'class' => 'form-control select2 select_supp_wh',
						                                                           'data-url' => Url::toRoute(['part/get-parts-by-floc'])
					                                                           ])->label(false)?>
				</div>

			</div>
		<? }else{ ?>
			<div class="col-lg-4">
				<div class="form-group">
					<label class="control-label"><?=Yii::t('app', 'From supplier warehouse')?></label>
					<label class="form-control" style="font-weight: normal;background-color: #f5f5f5;"><?=$model->fromWarehouse->name?></label>
				</div>
				<?=$form->field($model, 'from_warehouse_id')->hiddenInput()->label(false);?>
			</div>
		<? } ?>



		<? if($isNewRecord ?? null){ ?>
			<div class="col-lg-4">
				<?=$form->field($model, 'to_warehouse_id')->dropDownList($user_warehouses, ['prompt' => Yii::t('app', 'Select...'), 'class' => 'form-control select2'])?>
			</div>
		<? }else{ ?>
			<div class="col-lg-4">
				<div class="form-group">
					<label class="control-label"><?=Yii::t('app', 'Warehouse B')?></label>
					<label class="form-control" style="font-weight: normal;background-color: #f5f5f5;"><?=$model->toWarehouse->name?></label>
				</div>
				<?=$form->field($model, 'to_warehouse_id')->hiddenInput()->label(false);?>
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
		'isRecDaval' => true,
	])?>


	<div class="form-group pull-right">
		<?=Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
		<?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
