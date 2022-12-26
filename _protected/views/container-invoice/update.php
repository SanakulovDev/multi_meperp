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
	  'update' => $update ? true : false,
      'modelInvoice' => ($modelInvoice ?? null),
    ]
  )?>

<div id="multistepsform">
  <!-- progressbar -->
  <ul id="progressbar">
    <li>Создание контракта</li>
    <li>Создание заказа</li>
    <li class="active">Создание счёт фактуры</li>
  </ul>
</div>
<style>
#multistepsform {
	 width: 640px;
	 margin: 50px auto;
	 text-align: center;
	 position: relative;
}
 #multistepsform #progressbar {
	 margin-bottom: 30px;
	 overflow: hidden;
	 counter-reset: step;
}
 #multistepsform #progressbar li {
	 list-style-type: none;
	 color: #679b9b;
	 text-transform: uppercase;
	 font-size: 12px;
	 width: 33.33%;
	 float: left;
	 position: relative;
}
 #multistepsform #progressbar li:before {
	 content: counter(step);
	 counter-increment: step;
	 width: 45px;
	 line-height: 20px;
	 display: block;
	 font-size: 10px;
	 color: #fff;
	 background: #ff9a76;
	 border-radius: 3px;
	 margin: 0 auto 5px auto;
}
 #multistepsform #progressbar li:after {
	 content: "";
	 width: 100%;
	 height: 2px;
	 background: #ff9a76;
	 position: absolute;
	 left: -50%;
	 top: 9px;
	 z-index: -1;
}
 #multistepsform #progressbar li:first-child:after {
	 content: none;
}
 #multistepsform #progressbar li.active {
	 color: #00a65a;
}
 #multistepsform #progressbar li.active:before, #multistepsform #progressbar li.active:after {
	 background: #00a65a;
	 color: white;
}
</style>

</div>
