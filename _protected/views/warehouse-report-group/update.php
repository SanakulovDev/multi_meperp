<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\WarehouseReportGroup */
	$this->title = Yii::t('app', 'Update');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Warehouse report groups'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="warehouse-report-group-update">
	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
