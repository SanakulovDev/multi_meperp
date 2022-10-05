<?php
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\PartPackingSearch */
	/* @var $form yii\widgets\ActiveForm */
?>

<div class="part-packing-search">

	<?php $form = ActiveForm::begin([
		                                'action' => ['index'],
		                                'method' => 'get',
		                                'options' => [
			                                'data-pjax' => 1
		                                ],
	                                ]); ?>

	<?=$form->field($model, 'id')?>

	<?=$form->field($model, 'part_id')?>

	<?=$form->field($model, 'supplier_id')?>

	<?=$form->field($model, 'expandable')?>

	<?=$form->field($model, 'pack_qty')?>

	<?php // echo $form->field($model, 'piece_weight') ?>

	<?php // echo $form->field($model, 'net_weight') ?>

	<?php // echo $form->field($model, 'gross_weight') ?>

	<?php // echo $form->field($model, 'level1_pack_id') ?>

	<?php // echo $form->field($model, 'level2_pack_id') ?>

	<?php // echo $form->field($model, 'pack_pack_qty') ?>

	<?php // echo $form->field($model, 'full_gross_weight') ?>

	<?php // echo $form->field($model, 'created_by') ?>

	<?php // echo $form->field($model, 'created_at') ?>

	<?php // echo $form->field($model, 'updated_by') ?>

	<?php // echo $form->field($model, 'updated_at') ?>

	<div class="form-group">
		<?=Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary'])?>
		<?=Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary'])?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
