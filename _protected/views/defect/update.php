<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\Defect */
	$this->title = Yii::t('app', 'Update Defect: {name}', [
		'name' => $model->id,
	]);
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Defects'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="defect-update">
	<?=$this->render('_form', [
		'model' => $model,
	])?>
</div>
