<?php
	use app\models\InvoiceDetailSearch;
	use yii\helpers\Html;

	/* @var $this yii\web\View */
	/* @var $model app\models\ContainerInvoice */
	$this->title = Yii::t('app', 'Add detail: {nameAttribute}', [
		'nameAttribute' => $model->invoice->invoice_no.'('.$model->container->container_no.')',
	]);
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Container Invoices'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $model->invoice->invoice_no.'('.$model->container->container_no.')', 'url' => ['view', 'id' => $model->id]];
?>
<div class="container-invoice-view">
	<?=$this->render('cont_inv_header', [
		                                  'model' => $model,
		                                  'errorlist' => $errorlist ?? null,
		                                  'items' => $items,
		                                  'modelContainer' => $modelContainer,
		                                  'modelItems' => $modelItems,
		                                  'modelInvoice' => $modelInvoice,
		                                  'searchModel' => $searchModel,
	                                  ]
	)?>
	<hr>

	<p class="pull-right">
		<?=Html::a(Yii::t('app', 'Add detail'), ['/invoice-detail/create', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm'])?>
	</p>
	<?
		$searchModel = new InvoiceDetailSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$invoiceDetailModel = $model->invoiceDetails;
		echo $this->render('../invoice-detail/__details',
		                   [
			                   'dataProvider' => $dataProvider,
			                   'searchModel' => $searchModel,
		                   ]
		);
	?>
</div>

