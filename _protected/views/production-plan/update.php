<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\ProductionPlan */
	$this->title = Yii::t('app', 'Update');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Production plans'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="production-plan-update">


	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
