<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\GtdInvoice */
	$this->title = Yii::t('app', 'Update Gtd Invoice: {name}', [
		'name' => $model->id,
	]);
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'GTD Invoices'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="gtd-invoice-update">
	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
