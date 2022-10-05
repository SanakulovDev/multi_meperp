<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\Warehouse */
	$this->title = Yii::t('app', 'Update warehouse: {nameAttribute}', [
		'nameAttribute' => $model->name,
	]);
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Warehouses'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="warehouse-update">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
