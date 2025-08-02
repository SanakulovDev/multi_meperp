<?php
/**
 * @var $this               yii\web\View
 * @var $model              app\models\FreightInvoiceDetail
 * @var $parentModel        app\controllers\FreightInvoiceDetailController
 * @var $currencies         app\controllers\FreightInvoiceDetailController
 * @var $containers         app\controllers\FreightInvoiceDetailController
 * @var $invoices           app\controllers\FreightInvoiceDetailController
 * @var $selectedInvoices   app\controllers\FreightInvoiceDetailController
 * @var $invoicePaymentType app\controllers\FreightInvoiceDetailController
 * @var $freightInvoices     app\controllers\FreightInvoiceDetailController
 */
$this->title = Yii::t('app', 'Create Freight invoice detail');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Freight invoice'), 'url' => ['/freight-invoice/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="freight-invoice-detail-create">

  <?
  if(isset($errMsg)) {
    echo '<div class="alert-danger alert fade in">'.$errMsg.'</div>';
  } ?>

  <?=$this->render('_form', [
    'parentModel' => $parentModel,
    'model' => $model,
    'containers' => $containers,
    'outContainers' => $outContainers,
    'outInvoices' => $outInvoices,
    'invoices' => $invoices,
    'selectedInvoices' => $selectedInvoices,
    'invoicePaymentType' => $invoicePaymentType,
    'freightInvoices' => $freightInvoices,
  ])?>

</div>
