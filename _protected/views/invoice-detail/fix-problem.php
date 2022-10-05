<?php
/* @var $this yii\web\View */
/* @var $model app\models\InvoiceDetail */
/* @var TYPE_NAME $containerInvoice */
/* @var TYPE_NAME $partNo */
/* @var TYPE_NAME $contractList */
$this->title = Yii::t('app', 'Fix invoice detail: {nameAttribute}', [
  'nameAttribute' => $model->contInv->invoice->invoice_no."(".$model->contInv->container->container_no.")",
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'invoice details'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Fix');
?>
<div class="invoice-detail-fix-problem">

  <?=$this->render('_form3', [
    'model' => $model,
    'containerInvoice' => $containerInvoice,
    'partNo' => $partNo,
    'contractList' => $contractList,
  ])?>

</div>
