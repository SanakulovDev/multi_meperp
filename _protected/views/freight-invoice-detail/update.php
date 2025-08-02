<?php
/**
 * @var $this               yii\web\View
 * @var $model              app\models\FreightInvoiceDetail
 * @var $parentModel        app\controllers\FreightInvoiceDetailController
 * @var $containers         app\controllers\FreightInvoiceDetailController
 * @var $invoices           app\controllers\FreightInvoiceDetailController
 * @var $selectedInvoices   app\controllers\FreightInvoiceDetailController
 * @var $invoicePaymentType app\controllers\FreightInvoiceDetailController
 * @var $freightInvoices     app\controllers\FreightInvoiceDetailController
 */
$this->title = Yii::t('app', 'Update Freight invoice detail: {name}', [
  'name' => $model->freightInvoice->invoice_no."(".$model->freightInvoice->invoice_date.")",
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Freight invoice'), 'url' => ['/freight-invoice/index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="freight-invoice-detail-update">

  <?
  if(isset($errMsg)) {
    echo '<div class="alert-danger alert fade in">'.$errMsg.'</div>';
  } ?>

  <?=$this->render('_form', [
    'model' => $model,
    'containers' => $containers,
    'outContainers' => $outContainers,
    'outInvoices' => $outInvoices,
    'parentModel' => $parentModel,
    'invoices' => $invoices,
    'selectedInvoices' => $selectedInvoices,
    'invoicePaymentType' => $invoicePaymentType,
    'freightInvoices' => $freightInvoices,
  ])?>
</div>
