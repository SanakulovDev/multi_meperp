<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\InvoiceDetail */
	$this->title = Yii::t('app', 'Update invoice detail: {nameAttribute}', [
		'nameAttribute' => $model->contInv->invoice->invoice_no."(".$model->contInv->container->container_no.")",
	]);
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'invoice details'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="invoice-detail-update">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
