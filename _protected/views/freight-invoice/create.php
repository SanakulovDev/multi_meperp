<?php
/**
 * @var $this          yii\web\View
 * @var $model         app\models\FreightInvoice
 * @var $errMsg        app\controllers\FreightInvoiceController
 * @var $invoiceType   app\controllers\FreightInvoiceController
 * @var $routes        app\controllers\FreightInvoiceController
 * @var $deliveryTerms app\controllers\FreightInvoiceController
 * @var $carriers      app\controllers\FreightInvoiceController
 * @var $currencies    app\controllers\FreightInvoiceController
 */
$this->title = Yii::t('app', 'Create Freight invoice');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Freight invoices'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="freight-invoice-create">

  <?
  if(isset($errMsg)) {
    echo '<div class="alert-danger alert fade in">'.$errMsg.'</div>';
  } ?>

  <?=$this->render('_form', [
    'model' => $model,
    'routes' => $routes,
    'deliveryTerms' => $deliveryTerms,
    'currencies' => $currencies,
    'carriers' => $carriers,
    'invoiceType' => $invoiceType,
  ])?>

</div>
