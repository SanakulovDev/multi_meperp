<?php
use app\components\Helpers;
use app\models\PartOrder;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PartOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/** @var TYPE_NAME $contract */
/** @var TYPE_NAME $deliveryTerm */
$this->title = Yii::t('app', 'Orders Supplier');
$this->params['breadcrumbs'][] = $this->title;
$canUpdate = Yii::$app->user->can('part-order-update');
$canDelete = Yii::$app->user->can('part-order-delete');
$canView = Yii::$app->user->can('part-order-view');
?>
<div class="part-order-index">

  <p class="pull-right">
    <?php
    if(Yii::$app->user->can('part-order-create'))
      echo Html::a(Yii::t('app', 'btn-create'), ['create'],
        [
          'class' => 'btn btn-success btn-sm',
          'style' => 'margin-right: 5px',
          'data-intro' => Yii::t('intro', 'add-new-record')
        ]);
    if(Yii::$app->user->can('part-order-xls'))
      echo Html::a(Yii::t('app', 'btn-download'), ['xls', 'PartOrderSearch' => ($_GET['PartOrderSearch'] ?? null)],
        [
          'class' => 'btn btn-info btn-sm searchPjax',
          'data-intro' => Yii::t('intro', 'download-button')
        ]);
    ?>
  </p>

  <?php Pjax::begin(['id' => 'pjaxGrid']); ?>
  <?=
  GridView::widget(
    [
      'dataProvider' => $dataProvider,
      'filterModel' => $searchModel,
      'summary' => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
      'options' => ['style' => 'overflow:auto;clear:both'],
      'emptyText' => Yii::t('app', 'No results found.'),
      'tableOptions' => [
        'class' => 'table-td-nowrap sm_filter table table-striped table-bordered table-condensed table-sm-padding_2_0',
        'data-intro' => Yii::t('intro', 'data-table')
      ],
      'filterRowOptions' => ['data-intro' => Yii::t('intro', 'filter')],
      'pager' => [
        'class' => '\yii\widgets\LinkPager',
        'options' => [
          'class' => 'pagination',
          'data-intro' => Yii::t('intro', 'pagination')
        ],
      ],
      'columns' => [
        [
          'class' => 'yii\grid\SerialColumn',
          'header' => '№',
          'headerOptions' => ['style' => 'width: 50px;text-align: center;color: #3c8dbc;'],
          'contentOptions' => ['style' => 'width: 50px;text-align: center;']
        ],
        [
          'class' => 'yii\grid\ActionColumn',
          'template' => '{update} {delete}',
          'header' => '<i class="fa fa-fw fa-gears"></i>',
          'headerOptions' => ['style' => 'width:70px; text-align: center;vertical-align:middle;color: #3c8dbc;'],
          'contentOptions' => ['style' => 'width:70px; text-align: center;vertical-align:middle;'],
          'buttons' => [
            'update' => function($url, $model) use ($canUpdate) {
              if(!$canUpdate) return false;

              return Html::a(
                '<span  class="glyphicon glyphicon-pencil"></span>',
                $url,
                [
                  'title' => Yii::t('app', 'Update')
                ]
              );
            },
            'delete' => function($url, $model) use ($canDelete) {
              if(!$canDelete) return false;

              return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                $url,
                [
                  'data-pjax' => 0,
                  'data-confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                  'data-method' => 'post',
                  'title' => Yii::t('app', 'Delete')
                ]);
            }
          ],
          'visible' => $canDelete || $canUpdate || $canView
        ],
        [
          'attribute' => 'order_no',
          'headerOptions' => ['style' => 'vertical-align:middle;'],
          'contentOptions' => ['style' => 'right;vertical-align:middle;'],
          'content' => function($model) use ($canView) {
            return $canView ? Html::a($model->order_no, Url::toRoute(['part-order/view', 'id' => $model->id])) : $model->order_no;
          },
        ],
        [
          'attribute' => 'order_type',
          'headerOptions' => ['style' => 'width:100px; text-align: center;vertical-align:middle;'],
          'contentOptions' => ['style' => 'width:100px; text-align: center;vertical-align:middle;'],
          'filter' => $searchModel->orderTypeList,
          'content' => function($model, $column) {
            return $model->orderTypeText;
          }
        ],
        [
          'attribute' => 'iss_dt',
          'headerOptions' => ['style' => 'width:100px; text-align: center;vertical-align:middle;'],
          'contentOptions' => ['style' => 'width:100px; text-align: center;vertical-align:middle;'],
        ],
        [
          'attribute' => 'mr_dt',
          'headerOptions' => ['style' => 'width:100px; text-align: center;vertical-align:middle;'],
          'contentOptions' => ['style' => 'width:100px; text-align: center;vertical-align:middle;'],
        ],
        [
          'attribute' => 'for_month',
          'headerOptions' => ['style' => 'width:100px; text-align: center;vertical-align:middle;'],
          'contentOptions' => ['style' => 'width:100px; text-align: center;vertical-align:middle;'],
        ],
        [
          'attribute' => 'contract_id',
          'value' => 'contract.contract_no',
          'headerOptions' => ['style' => 'width:150px; text-align: center;vertical-align:middle;'],
          'contentOptions' => ['style' => 'width:150px; text-align: center;vertical-align:middle;'],
          'filter' => Html::activeDropDownList($searchModel, 'contract_id', $contract, ['class' => 'form-control select2', 'prompt' => '...']),
        ],
        [
          'attribute' => 'delivery_term_id',
          'value' => 'deliveryTerm.name',
          'headerOptions' => ['style' => 'width:150px; text-align: center;vertical-align:middle;'],
          'contentOptions' => ['style' => 'width:150px; text-align: center;vertical-align:middle;'],
          'filter' => Html::activeDropDownList($searchModel, 'delivery_term_id', $deliveryTerm, ['class' => 'form-control select2', 'prompt' => '...']),
        ],
        [
          'header' => Yii::t('app','Currency'),
          'headerOptions' => ['style' => 'text-align: center;vertical-align:middle;color: #3c8dbc;'],
          'contentOptions' => ['style' => 'text-align: right;vertical-align:middle;'],
          'value' => function($model) {
            return $model->contract->currency->code;
          }
        ],
        [
          'attribute' => 'amount',
          'headerOptions' => ['style' => 'width:150px; text-align: right;vertical-align:middle;color: #3c8dbc;'],
          'contentOptions' => ['style' => 'width:150px; text-align: right;vertical-align:middle;'],
          'filter' => false,
          'content' => function($model) {
            return Helpers::numberFormatRemoveZero($model->amount, 0, '.', " ", true, true);
          },
        ],
        [
          'header' => Yii::t('app','Shipped amount'),
          'headerOptions' => ['style' => 'text-align: center;vertical-align:middle;color: #3c8dbc;'],
          'contentOptions' => ['style' => 'text-align: right;vertical-align:middle;'],
          'value' => function($model) {
            $sumInvDetal = PartOrder::getInvoiceDetailAmount($model->id);
            return  ($sumInvDetal > 0) ? Helpers::numberFormatRemoveZero($sumInvDetal, 0) : null;
          }
        ],
        [
          'header' => Yii::t('app','Balance'),
          'headerOptions' => ['style' => 'text-align: center;vertical-align:middle;color: #3c8dbc;'],
          'contentOptions' => ['style' => 'text-align: right;vertical-align:middle;'],
          'format' => 'raw',
          'value' => function($model) {
            $balance = ($model->amount) - (PartOrder::getInvoiceDetailAmount($model->id));
            $balanceAmt = Helpers::numberFormatRemoveZero($balance, 0);
            $bClass = ($balance != 0) ? "minus":'';
            return Html::tag('p',
              Html::encode(($balance != 0) ? $balanceAmt : null),
              ['class'=> $bClass,'style'=>'margin:0 0 0 0']
            );

          }
        ],
      ],
    ]
  );?>

  <?php Pjax::end(); ?>

</div>
