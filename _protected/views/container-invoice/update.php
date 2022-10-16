<?php
/* @var $this yii\web\View */
/* @var $model app\models\ContainerInvoice */
$this->title = Yii::t('app', 'Update invoice').': '.$model->invoice->invoice_no.'('.$model->container->container_no.')';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Container Invoices'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->invoice->invoice_no.'('.$model->container->container_no.')', 'url' => ['view', 'container_id' => $model->container_id, 'invoice_id' => $model->invoice_id, 'shipped_at' => $model->shipped_at]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="container-invoice-update">

  <?=$this->render('_form', [
      'model' => $model,
      'modelContainer' => ($modelContainer ?? null),
      'modelItems' => ($modelItems ?? null),
      'errorlist' => ($errorlist ?? null),
      'items' => ($items ?? null),
      'contract' => $invoice ?? null,
      'modelInvoice' => ($modelInvoice ?? null),
    ]
  )?>

</div>
