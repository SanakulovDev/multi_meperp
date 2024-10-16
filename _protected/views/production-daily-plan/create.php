<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\ProductionPlan */
	$this->title = Yii::t('app', 'Create');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Production plans'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="production-plan-create">
	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
