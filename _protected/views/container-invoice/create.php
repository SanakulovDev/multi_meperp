<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\ContainerInvoice */
	$this->title = Yii::t('app', 'Create invoice')."(Header)";
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Container Invoices'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-invoice-create">

	<?=$this->render('_form', [
		'model' => $model ?? null,
		'modelContainer' => $modelContainer ?? null,
		'modelItems' => $modelItems ?? null,
		'errorlist' => $errorlist ?? null,
		'items' => $items ?? null,
		'partOrder' => $partOrder ?? null,
		'contract' => $contract ?? null,
		'modelInvoice' => $modelInvoice ?? null,
	])?>

</div>
