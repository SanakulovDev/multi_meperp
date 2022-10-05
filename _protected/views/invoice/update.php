<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\Invoice */
	$this->title = Yii::t('app', 'Update Invoice: {name}', [
		'name' => $model->id,
	]);
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Invoices'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="invoice-update">

	<?=$this->render('_form', [
		'errorlist' => ($errorlist ?? null),
		'model' => ($model ?? null),
		'items' => ($items ?? null),
		'modelItems' => ($modelItems ?? null),
	])?>

</div>
