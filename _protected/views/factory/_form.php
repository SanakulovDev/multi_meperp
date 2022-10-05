<?php
	use app\models\Warehouse;
	use yii\helpers\ArrayHelper;
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\Factory */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="factory-form">

	<?php $form = ActiveForm::begin(); ?>
	<div class="row">
		<div class="col-sm-1 col-md-1 col-lg-1">
			<?=$form->field($model, 'is_main')
			        ->dropDownList([
				                       '1' => Yii::t('app', 'Yes'),
				                       '0' => Yii::t('app', 'No')
			                       ])?>
		</div>
		<div class="col-sm-3 col-md-3 col-lg-3">
			<?=$form->field($model, 'name')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-sm-2 col-md-2 col-lg-2">
			<?=$form->field($model, 'alias')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-sm-3 col-md-3 col-lg-3">
			<?php
				$fgWhs = Warehouse::find()->where(['in','warehouse_type', [Warehouse::TYPE_PHYSICAL, Warehouse::TYPE_SHOP]])->all();
				$items = ArrayHelper::map($fgWhs, 'id', 'name');
				$params = ['prompt' => '. . .'];
				echo $form->field($model, 'fg_warehouse_id')->dropDownList($items, $params);
			?>
		</div>
		<div class="col-sm-3 col-md-3 col-lg-3">
			<?=$form->field($model, 'head')->textInput(['maxlength' => true])?>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-3 col-md-3 col-lg-3">
			<?=$form->field($model, 'chief_accountant')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-sm-5">
			<?=$form->field($model, 'address')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-sm-4">
			<?=$form->field($model, 'remark')->textInput(['maxlength' => true])?>
		</div>
	</div>


	<div class="row">
		<div class="col-sm-3 col-md-3 col-lg-3">
			<?=$form->field($model, 'tin')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-sm-3 col-md-3 col-lg-3">
			<?=$form->field($model, 'vat')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-sm-3 col-md-3 col-lg-3">
			<?=$form->field($model, 'duns')->textInput(['maxlength' => true])?>
		</div>
		<div class="col-sm-3 col-md-3 col-lg-3">
			<?=$form->field($model, 'status')->dropDownList($model->statusList)?>
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
