<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\ProductionPlan */
	$this->title = Yii::t('app', 'Upload Production plan');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Production plans'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>


<div class="production-plan-create">
	<?=$this->render('_uploadform', [
		'model' => $model,
		'model_uploadForm' => $model_uploadForm ?? null
	])?>
</div>
