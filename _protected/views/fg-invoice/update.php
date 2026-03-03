<?php
/* @var $this yii\web\View */
/* @var $model app\models\FgInvoice */
/** @var TYPE_NAME $detail_count */
$this->title = Yii::t('app', 'Update FG Invoice(TTN): {name}', ['name' => $model->invoice_no."(".$model->invoice_date.")",]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'FG Invoice'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
use app\models\FgInvoiceDetailSearch;
use yii\helpers\Html;

?>
<div class="fg-invoice-update">

  <?=
  $this->render('_header_update', [
    'model' => $model,
    'detail_count' => $detail_count
  ])?>

  <div class="row">
    <div class="col-lg-12">
      <?php if(Yii::$app->user->can('fg-invoice-detail-create')): ?>
        <?=Html::a(Yii::t('app', 'btn-create'), ['../fg-invoice-detail/create', 'fg_inv_id' => $model->id], ['class' => 'btn btn-primary btn-sm'])?>
      <?php endif; ?>
    </div>
  </div>
  <?
  $param = Yii::$app->request->queryParams;
  $searchModel = new FgInvoiceDetailSearch(['fg_invoice_id' => $param['id']]);
  $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
  echo $this->render(
    '../fg-invoice-detail/__details',
    [
      'dataProvider' => $dataProvider,
      'searchModel' => $searchModel,
    ]
  );
  ?>


</div>
