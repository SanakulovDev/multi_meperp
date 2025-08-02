<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\ProductionPlan */
	use yii\helpers\Html;
	use app\models\UploadForm;
	use yii\widgets\ActiveForm;

	$this->title = Yii::t('app', 'Upload');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'OEM'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12">
		<div class="form-group pull-right">
			<?=Html::a(Yii::t('app', 'btn-download-template'), '/public/oem_plan_template.xlsx', ['class' => 'btn btn-info btn-sm', 'data-intro' => Yii::t('intro', 'btn-download-template')])?>
		</div>
	</div>
</div>

<div class="production-plan-create">
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
<div class="row" style="vertical-align: middle">
	<div class="col-lg-6 col-md-6 col-sm-12">
		<?=$form->field($model, 'file')->fileInput()?>
	</div>
</div>
<hr>
<div class="row" style="vertical-align: middle">
	<div class="col-lg-12 col-md-12 col-sm-12">
		<?=Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
		<?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
	</div>
</div>
<?php ActiveForm::end() ?>
</div>
