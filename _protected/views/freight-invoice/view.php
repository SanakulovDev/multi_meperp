<?php
use app\models\FreightInvoiceDetailSearch;
use yii\helpers\Html;

/**
 * @var $this        yii\web\View
 * @var $model       app\models\FreightInvoice
 * @var $parentModel app\controllers\FreightInvoiceController
 * @var $invoiceType app\controllers\FreightInvoiceController
 * @var $containers  app\controllers\FreightInvoiceController
 * @var $currencies  app\controllers\FreightInvoiceController
 */
$this->title = Yii::t('app', 'Freight invoices').": ".$model->invoice_no."(".$model->invoice_date.")";
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Freight invoices'), 'url' => ['index']];
?>
<div class="freight-invoice-view">

	<div class="row ">
		<div class="col-sm-2">
      <?=Html::a(Yii::t('app', 'btn-back'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
		</div>
		<div class="col-sm-10">
			<div class="pull-right">
        <?=Html::input('hidden', 'shu_id', $model->id);
        if(empty($model->document_id)) {
          if(Yii::$app->user->can('freight-invoice-detail-create')) {
            echo Html::a('<i class="glyphicon glyphicon-plus"></i> '.Yii::t('app', 'Add container'), ['/freight-invoice-detail/create', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm']);
          }
          if(Yii::$app->user->can('container-invoice-update')) {
            echo "&nbsp;".Html::a(Yii::t('app', 'btn-update'), ['update', 'id' => $model->id], ['class' => 'btn btn-warning btn-sm']);
          }
        }
        ?>

			</div>
		</div>
	</div>
	<br>
  <?=$this->render(
    'freight-invoice-header',
    [
      'parentModel' => $model,
    ]
  )
  ?>
	<hr class="hr_style1">
  <?
  $param = Yii::$app->request->queryParams;
  $searchModel = new FreightInvoiceDetailSearch(['freight_invoice_id' => $param['id']]);
  $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
  echo $this->render(
    '../freight-invoice-detail/_details',
    [
      'dataProvider' => $dataProvider,
      'searchModel' => $searchModel,
      'containers' => $containers,
    ]
  );
  ?>


</div>
