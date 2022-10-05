<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\FgInvoiceDetail */
	$this->title = Yii::t('app', 'Update invoice {inv_name} item:{inv_detail_name}',
	                      [
		                      'inv_name' => $model->fgInvoice->invoice_no."(".$model->fgInvoice->invoice_date.")",
		                      'inv_detail_name' => $model->part_no,
	                      ]
	);
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Fg invoice details'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $model->part_no, 'url' => ['view', 'id' => $model->id]];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="fg-invoice-detail-update">
	<?=$this->render('_form', [
		'model' => $model,
	])?>
</div>
