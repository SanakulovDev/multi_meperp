<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\ProductionPlan */
	use yii\helpers\Html;

	$this->title = Yii::t('app', 'Upload Production plan');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Production plans'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12">
		<div class="form-group pull-right">
			<?=Html::a(Yii::t('app', 'btn-download-template'), ['download-template'], ['class' => 'btn btn-warning btn-sm'])?>
		</div>
	</div>
</div>

<div class="production-plan-create">
	<?=$this->render('_uploadform', [
		'model' => $model,
		'model_uploadForm' => $model_uploadForm ?? null
	])?>
</div>
