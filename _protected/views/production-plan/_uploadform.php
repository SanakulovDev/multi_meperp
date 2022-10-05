<?php
	/**
		* @var UploadForm $model_uploadForm
		*/
	/* @var $shift app\controllers\ProductionPlanController */
	/* @var $warehouse_id app\controllers\ProductionPlanController */
	use app\models\UploadForm;
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

?>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

<div class="row">
	<div class="col-xs-12">
		<?=$form->field($model_uploadForm, 'xls_file')->fileInput()?>
	</div>
</div>
<hr class="hr_style1">
<div class="form-group pull-right">
	<?=Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
	<?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
</div>
<?php ActiveForm::end() ?>
