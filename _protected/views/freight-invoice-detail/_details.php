<?php
use app\components\Helpers;
use app\enums\FreightInvoiceType;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var $containers app\controllers\FreightInvoiceDetailController
 * @var $currencies app\controllers\FreightInvoiceDetailController
 */
$canUpdate = Yii::$app->user->can('freight-invoice-detail-update');
$canDelete = Yii::$app->user->can('freight-invoice-detail-delete');
$select2Param = ['class' => 'form-control select2', 'prompt' => Yii::t('app', 'All')];
?>

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
      [
        'class' => 'yii\grid\SerialColumn',
        'header' => '№',
        'headerOptions' => ['style' => 'width: auto;text-align: center;color: #3c8dbc;'],
        'contentOptions' => ['style' => 'width: auto;text-align: center;']
      ],
      [
        'controller' => 'freight-invoice-detail',
        'class' => 'yii\grid\ActionColumn',
        'template' => '{view}{update}{delete} ',
        'header' => '<i class="fa fa-fw fa-gears"></i>',
        'headerOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
        'contentOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;'],
        'buttons' => [
          'update' => function($url, $model) use ($canUpdate) {
            if(!$canUpdate)
              return false;
            if(!empty($model->contInv->document_id))
              return false;
            $url = Url::toRoute(['freight-invoice-detail/update', 'id' => $model->id]);
            return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', $url, [
              'title' => Yii::t('app', 'Edit')
            ]);
          },
          'delete' => function($url, $model) use ($canDelete) {
            if(!$canDelete)
              return false;
            if(!empty($model->contInv->document_id))
              return false;
            $url = Url::toRoute(['freight-invoice-detail/delete', 'id' => $model->id]);
            return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>', $url, [
              'title' => Yii::t('app', 'Delete'),
              'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
              ],
            ]);
          },
        ],
        'visible' => $canUpdate || $canDelete
      ],
      [
        'attribute' => 'container_id',
        'value' => 'container.container_no',
        'filter' => Html::activeDropDownList(
          $searchModel,
          'container_id',
          $containers,
          $select2Param
        ),
        'contentOptions' => function($model, $column) {
          return ['class' => 'td-nowrap'];
        }
      ],
      [
        'format' => 'raw',
        'attribute' => Yii::t('app', 'Supplier'),
        'headerOptions' => ['style' => 'text-align:center;vertical-align:middle;color:#3c8dbc;'],
        'contentOptions' => ['style' => 'vertical-align:middle;'],
        'value' => function($model) {
          return $model->supplierList();
        }
      ],
      [
        'attribute' => Yii::t('app', 'Amount'),
        'headerOptions' => ['style' => 'text-align:center;vertical-align:middle;color:#3c8dbc;'],
        'contentOptions' => ['style' => 'text-align:right;vertical-align:middle;'],
        'value' => function($model) {
          return Helpers::numberFormatRemoveZero($model->summCost());
        }
      ],
      'comment:ntext',

      [
        'attribute' => 'outbound_id',
        'value' => 'outboundInvoiceDetail.freightInvoice.invoiceInfo',
        'visible' => $searchModel->freightInvoice->isInbound
      ],

    ],
  ]
);
?>
