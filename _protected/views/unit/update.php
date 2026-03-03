<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\Unit */
	$this->title = Yii::t('app', 'Update', [
		'name' => $model->id,
	]);
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Units'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $model->unit_value, 'url' => ['view', 'id' => $model->id]];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="unit-update">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
