<?php

use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('app', 'Update');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Customs declaration'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="gtd-create">


	<div class="gtd-form">

		<?php $form = ActiveForm::begin(); ?>

		<div class="row">
			<div class="col-md-3 col-sm-3 col-lg-3">
				<?= $form->field($model, 'gtd_no')->textInput(['maxlength' => true, 'class' => 'form-control input-sm']) ?>
			</div>
			<div class="col-md-3 col-sm-3 col-lg-3">
				<?= $form->field($model, 'gtd_dt')->widget(DateTimePicker::classname(), [
					'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
					'layout' => '{picker}{input}{remove}',
					'removeButton' => ['position' => 'append'],
					'language' => 'ru',
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
						'class' => 'form-control input-sm'
					]
				]);
				?>
			</div>
			<div class="col-md-3 col-sm-3 col-lg-3">
				<?= $form->field($model, 'post_no')->textInput(['maxlength' => true, 'class' => 'form-control input-sm']) ?>
			</div>
		</div>
		<?
		if(isset($errorlist)){
			if(isset($errorlist) && strlen(trim($errorlist)) > 1){
				?>
		<div class='alert alert-danger'>
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
			<strong><?= Yii::t('app', 'Error') . '!!!<br>' ?></strong> <?= $errorlist; ?>
		</div>
		<?
			}
		}
	?>
		<hr>
		<div class="form-group pull-right">
			<?= Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm']) ?>
			<?= Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm']) ?>
		</div>
		<?php ActiveForm::end(); ?>
	</div>
</div>