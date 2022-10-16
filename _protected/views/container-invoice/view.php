<?php
use app\models\InvoiceDetailSearch;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ContainerInvoice */
$this->title = Yii::t('app', 'Container Invoices').": ".$model->invoice->invoice_no."(".$model->container->container_no.")";
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Container Invoices'), 'url' => ['index']];
//	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-invoice-view">

  <?php $form = ActiveForm::begin(['action' => ['import-detail']]); ?>
  <div class="row ">
    <div class="col-lg-12">
      <div class="pull-right">
        <?=Html::input('hidden', 'shu_id', $model->id);
        if (empty($model->document_id)) {
          if (Yii::$app->user->can('invoice-detail-create')) {
            echo Html::a('<i class="glyphicon glyphicon-plus"></i> '.Yii::t('app', 'Add detail'), ['/invoice-detail/create', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm']);
          }
          if (Yii::$app->user->can('container-invoice-update')) {
            echo "&emsp;".Html::a(Yii::t('app', 'btn-update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm']);
          }
          if (Yii::$app->user->can('container-invoice-import-detail')) {
            echo "&emsp;".Html::submitButton(Yii::t('app', 'btn-upload-file')." ".Yii::t('app', 'Upload detail'), ['class' => 'btn btn-success btn-sm']);
          }
        }
        if (Yii::$app->user->can('container-invoice-to-xlsx')) {
          echo "&emsp;".Html::a(Yii::t('app', 'btn-download'), ['to-xlsx', 'id' => $model->id], ['class' => 'btn btn-info btn-sm']);
        }?>

      </div>
    </div>
  </div>
  <?php ActiveForm::end(); ?>
  <?=
  $this->render(
    'cont_inv_header',
    [
      'model' => $model ?? null,
      'errorlist' => $errorlist ?? null,
      'items' => $items ?? null,
      'modelContainer' => $modelContainer ?? null,
      'modelItems' => $modelItems ?? null,
      'modelInvoice' => $modelInvoice ?? null,
      'searchModel' => $searchModel ?? null,
    ]
  )
  ?>
  <hr>
  <?
  $param = Yii::$app->request->queryParams;
  $searchModel = new InvoiceDetailSearch(['cont_inv_id' => $param['id']]);
  $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
  //	$dataProvider->setPagination(['pageSize' => 10]);
  echo $this->render(
    '../invoice-detail/__details',
    [
      'dataProvider' => $dataProvider,
      'searchModel' => $searchModel,
    ]
  );
  ?>
  <div style="text-align: right;">
  <?=Html::input('hidden', 'shu_id', $model->id);
        if (empty($model->document_id)) {
          if (Yii::$app->user->can('container-invoice-update')) {
            echo "<a href='/container-invoice/update?id=" . $model->id . "' class='btn btn-primary btn-sm'>Далее </a>";
          }
        } ?>
  </div>
</div>