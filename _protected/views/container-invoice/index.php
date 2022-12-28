<?php
use app\components\Helpers;
use app\enums\ContainerType;
use app\models\ShipMode;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ContainerInvoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $order_invoice_problem_cnt */
if(Yii::$app->user->can('invoice-part-problem-index')) {
  $bgColor = ($order_invoice_problem_cnt == 0) ? "btn-success" : "btn-danger";
  $problem_link = Html::a(
    Yii::t('app', 'Order-invoice problem/index').' <span class="badge">'.$order_invoice_problem_cnt.'</span>',
    ['/invoice-part-problem/'],
    [
      'target' => '_blank',
      'class' => 'btn btn-sm '.$bgColor,
      'data-intro' => Yii::t('intro', 'Order-invoice problem')
    ]
  );
} else {
  $problem_link = '';
}
$title = Yii::t('app', 'Advanced Shipment Notification (ASN)');
$this->title = $title."&emsp;".$problem_link;
$this->params['breadcrumbs'][] = $title;
// '{update-awb}{view}&nbsp;{update}{delete}{update-regime}{create-document}{remove-document}'
$canUpdate = Yii::$app->user->can('container-invoice-update');
$canDelete = Yii::$app->user->can('container-invoice-delete');
$canView = Yii::$app->user->can('container-invoice-view');
$canUpdateAwb = Yii::$app->user->can('container-invoice-update-awb');
$canUpdateRegime = Yii::$app->user->can('container-invoice-update-regime');
$canCreateDocument = Yii::$app->user->can('container-invoice-create-document');
$canRemoveDocument = Yii::$app->user->can('container-invoice-remove-document');
?>
<div class="container-invoice-index">
	<div class="row">
		<div class="col-lg-12">
			<p class="pull-right">
        <? if(Yii::$app->user->can('container-invoice-create')) { ?>
          <?=Html::a(
            '<i class="glyphicon glyphicon-plus"></i> '.Yii::t('app', 'Create invoice'),
            ['create'],
            [
              'class' => 'btn btn-success btn-sm',
              'data-intro' => Yii::t('intro', 'add-new-record')
            ]
          )?>
        <? } ?>
        <? if(Yii::$app->user->can('container-invoice-cont_inv-xlsx')) { ?>
          <?=Html::a(
            Yii::t('app', 'btn-download'),
            ['cont_inv-xlsx', 'ContainerInvoiceSearch' => ($_GET['ContainerInvoiceSearch'] ?? null)],
            [
              'class' => 'btn btn-info btn-sm',
              'data-intro' => Yii::t('intro', 'download-button')
            ]
          )?>
        <? } ?>
			</p>
		</div>

	</div>
  <?
  if(!empty($errorlist)) {
    echo '<div class="alert-danger alert fade in">';
    echo '<strong>'.Yii::t('app', 'Error').'</strong>';
    foreach($errorlist as $err_index => $err_value) {
      echo "<p><strong>".$err_index.":</strong> ".$err_value.'</p>';
    }
    echo '</div>';
  }
  ?>

  <?php Pjax::begin(['id' => 'pjaxGrid']); ?>
  <?=GridView::widget(
    [
      'dataProvider' => $dataProvider,
      'filterModel' => $searchModel,
      'summary' => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
      'options' => ['style' => 'overflow:auto;clear:both'],
      'emptyText' => Yii::t('app', 'No results found.'),
      'tableOptions' => [
        'class' => 'sm_filter table table-striped table-bordered table-condensed table-sm-padding_2_0',
        'data-step' => 4,
        'data-intro' => Yii::t('intro', 'data-table')
      ],
      'filterRowOptions' => ['data-step' => 5, 'data-intro' => Yii::t('intro', 'filter')],
      'pager' => [
        'class' => '\yii\widgets\LinkPager',
        'options' => [
          'class' => 'pagination',
          'data-step' => 6,
          'data-intro' => Yii::t('intro', 'pagination')
        ],
      ],
      'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
          'class' => 'yii\grid\ActionColumn',
          'template' => '{update-awb}{view}{update}{delete}{update-regime}{create-document}{remove-document}',
          'header' => '<i class="fa fa-fw fa-gears"></i>',
          'headerOptions' => ['style' => 'min-width:100px; width:auto; text-align: center;vertical-align:middle;color: #3c8dbc;'],
          'contentOptions' => ['style' => 'min-width:100px; width:auto; text-align: center;vertical-align:middle;'],
          'buttons' => [
            'update-awb' => function($url, $model) use ($canUpdateAwb) {
              if(!$canUpdateAwb)
                return false;
              $url = Url::toRoute(['container-invoice/update-awb', 'id' => $model->id]);
              if(!empty($model->arrived_at) || strtoupper($model->shipMode->name) != 'AIR')
                return false;
              return Html::a(
                  '<span  class="glyphicon glyphicon-plane"></span>',
                  false,
                  [
                    'class' => 'modalButtonUpdate',
                    'value' => $url,
                    'title' => Yii::t('app', 'Update AWB')
                  ]
                ).'&nbsp;';
            },
            'view' => function($url, $model) use ($canView) {
              if(!$canView)
                return false;
              $url = Url::toRoute(['container-invoice/view', 'id' => $model->id]);
              return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>', $url, [
                  'title' => Yii::t('app', 'View')
                ]).'&nbsp;';
            },
            'update' => function($url, $model) use ($canUpdate) {
              if(!$canUpdate)
                return false;
              if(!empty($model->document_id))
                return false;
              $url = Url::toRoute(['container-invoice/update', 'id' => $model->id, 'update' => 1]);
              return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', $url, [
                  'title' => Yii::t('app', 'Edit')
                ]).'&nbsp;';
            },
            'delete' => function($url, $model) use ($canDelete) {
              if(!$canDelete)
                return false;
              if(!empty($model->document_id))
                return false;
              $url = Url::toRoute(['container-invoice/delete', 'id' => $model->id]);
              return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>', $url, [
                  'title' => Yii::t('app', 'Delete'),
                  'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                  ],
                ]).'&nbsp;';
            },
            'update-regime' => function($url, $model) use ($canUpdateRegime) {
              if(!$canUpdateRegime)
                return false;
              if(empty($model->arrived_at))
                return false;
              $url = Url::toRoute(['container-invoice/update-regime', 'id' => $model->id]);
              return Html::a('<i class="fa fa-fw fa-cube"></i>', $url, [
                  'title' => Yii::t('app', 'Update regime'),
                ]).'&nbsp;';
            },
            'create-document' => function($url, $model) use ($canCreateDocument) {
              if(!$canCreateDocument)
                return false;
              if(!(!empty($model->arrived_at) and empty($model->document_id)))
                return false;
              $url = Url::toRoute(['container-invoice/create-document', 'id' => $model->id]);
              return Html::a('<span class="fa fa-file-text" aria-hidden="true"></span>', $url, [
                  'title' => Yii::t('app', 'Create document'),
                  'class' => 'aButton',
                  'id' => 'a'.$model->id,
                  'data' => [
                    'confirm' => Yii::t('app', 'Are you sure?'),
                    'method' => 'post',
                  ],
                ]).'&nbsp;';
            },
            'remove-document' => function($url, $model) use ($canRemoveDocument) {
              if(!$canRemoveDocument)
                return false;
              if(empty($model->document_id))
                return false;
              $url = Url::toRoute(['container-invoice/remove-document', 'id' => $model->id]);
              return Html::a('<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>', $url, [
                  'title' => Yii::t('app', 'Remove document'),
                  'data' => [
                    'confirm' => Yii::t('app', 'Are you sure?'),
                    'method' => 'post',
                  ],
                ]).'&nbsp;';
            },
          ],
          'visible' => $canUpdate || $canDelete || $canView || $canUpdateAwb || $canUpdateRegime || $canCreateDocument || $canRemoveDocument
        ],
        [
          'attribute' => 'invoice_id',
          'value' => 'invoice.invoice_no',
          'headerOptions' => ['style' => 'min-width:150px; width:auto; text-align: center;vertical-align:middle;color: #3c8dbc;'],
          'contentOptions' => function($model, $column) {
            return ['title' => $model->invoice->invoice_no ?? null, 'class' => 'td-nowrap', 'style' => 'max-width:250px'];
          }
        ],
        [
          'attribute' => 'container_id',
          'label' => Yii::t('app', 'CONT./TRUCK/AWBL'),
          'headerOptions' => ['style' => 'min-width:150px; width:auto; text-align: center;vertical-align:middle;color: #3c8dbc;'],
          'contentOptions' => ['style' => 'min-width:150px; width:auto; vertical-align:middle;', 'class' => 'x-editable'],
          'value' => 'container.container_no',
        ],
        [
          'attribute' => 'ship_mode_id',
          'headerOptions' => ['style' => 'width:80px;text-align: center;vertical-align:middle;'],
          'contentOptions' => ['style' => 'width:80px;text-align: center;vertical-align:middle;'],
          'value' => 'shipMode.name',
          'filter' => yii\helpers\ArrayHelper::map(ShipMode::find()->all(), 'id', 'name'),
        ],
        [
          'attribute' => 'container_type',
          'headerOptions' => ['style' => 'width:80px;text-align: center;vertical-align:middle;'],
          'contentOptions' => ['style' => 'width:80px;text-align: center;vertical-align:middle;'],
          'value' => 'container.container_type',
          'filter' => ContainerType::list(),
        ],
        [
          'attribute' => 'net_weight',
          'headerOptions' => ['style' => 'width:120px;text-align: right;vertical-align:middle;'],
          'contentOptions' => ['style' => 'width:120px;text-align: right;vertical-align:middle;'],
          'value' => function($data) {
            return Helpers::numberFormatRemoveZero($data->net_weight, 2, '.', "", true, true);
          }
        ],
        [
          'attribute' => 'gross_weight',
          'headerOptions' => ['style' => 'width:120px;text-align: right;vertical-align:middle;'],
          'contentOptions' => ['style' => 'width:120px;text-align: right;vertical-align:middle;'],
          'value' => function($data) {
            return Helpers::numberFormatRemoveZero($data->gross_weight, 2, '.', "", true, true);
          }
        ],
        [
          'attribute' => 'cbm',
          'headerOptions' => ['style' => 'width:120px;text-align: right;vertical-align:middle;'],
          'contentOptions' => ['style' => 'width:120px;text-align: right;vertical-align:middle;'],
          'value' => function($data) {
            return Helpers::numberFormatRemoveZero($data->cbm, 2, '.', "", true, true);
          }
        ],
        [
          'attribute' => 'shipped_at',
          'headerOptions' => ['style' => 'min-width:100px;width:auto;text-align: center;vertical-align:middle;'],
          'value' => function($data) {
            return $data->shipped_at;
          },
          'contentOptions' => function($model, $key, $index, $column) {
            return [
              'style' => 'min-width:100px; width:auto;text-align:center;vertical-align:middle;',
              'title' => (!empty($model->shipped_by)) ? $model->shippedBy->username : '-'
            ];
          }
        ],
        [
          'attribute' => 'app_arr_at',
          'headerOptions' => ['style' => 'min-width:100px; width:auto;text-align:center;vertical-align:middle;'],
          'contentOptions' => ['style' => 'min-width:100px; width:auto;text-align:center;vertical-align:middle;'],
          'value' => 'app_arr_at'
        ],
        [
          'attribute' => 'station_date',
          'headerOptions' => ['style' => 'min-width:100px; width:auto;text-align:center;vertical-align:middle;'],
          'value' => function($data) {
            return $data->station_date;
          }
        ],
        [
          'attribute' => 'arrived_at',
          'headerOptions' => ['style' => 'min-width:100px; width:auto;text-align:center;vertical-align:middle;'],
          'value' => function($data) {
            return $data->arrived_at;
          },
          'contentOptions' => function($model, $key, $index, $column) {
            return [
              'style' => 'min-width:100px; width:auto;text-align:center;vertical-align:middle;',
              'title' => (!empty($model->arrived_by)) ? $model->arrivedBy->username : '-'
            ];
          }
        ],
        [
          'attribute' => 'received_at',
          'headerOptions' => ['style' => 'min-width:100px; width:auto;text-align: center;vertical-align:middle;'],
          'value' => function($data) {
            return $data->received_at;
          },
          'contentOptions' => function($model, $key, $index, $column) {
            return [
              'style' => 'min-width:100px; width:auto;text-align: center;vertical-align:middle;',
              'title' => (!empty($model->received_by)) ? $model->receivedBy->username : '-'
            ];
          }
        ],
        [
          'attribute' => 'document_id',
          'headerOptions' => ['style' => 'min-width:100px; width:auto;text-align:center;vertical-align:middle;'],
          'contentOptions' => ['style' => 'min-width:100px; width:auto;text-align:center;vertical-align:middle;'],
          'value' => 'document.docnum',
        ],
        [
          'attribute' => 'need_at',
          'headerOptions' => ['style' => 'min-width:100px; width:auto;text-align:center;vertical-align:middle;'],
          'contentOptions' => ['style' => 'min-width:100px; width:auto;text-align:center;vertical-align:middle;'],
          'value' => 'need_at'
        ],
        [
          'attribute' => 'current_locate',
          'headerOptions' => ['style' => 'min-width:100px; width:auto;text-align:center;vertical-align:middle;'],
          'contentOptions' => function($model, $column) {
            return ['class' => 'td-nowrap'];
          }
        ],
        [
          'attribute' => 'current_at',
          'headerOptions' => ['style' => 'min-width:100px; width:auto;text-align:center;vertical-align:middle;'],
          'contentOptions' => ['style' => 'min-width:100px; width:auto;text-align:center;vertical-align:middle;'],
          'value' => 'current_at'
        ],
      ],
    ]
  );
  ?>
  <?php Pjax::end(); ?>
</div>
