<?php
use app\components\Helpers;
use app\models\Contract;
use app\models\Part;
use app\models\PartOrder;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$canUpdate = Yii::$app->user->can('invoice-detail-update');
$canDelete = Yii::$app->user->can('invoice-detail-delete');
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
        'controller' => 'invoice-detail',
        'class' => 'yii\grid\ActionColumn',
        'template' => '{update}{delete} ',
        'header' => '<i class="fa fa-fw fa-gears"></i>',
        'headerOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
        'contentOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;'],
        'buttons' => [
          'update' => function($url, $model) use ($canUpdate) {
            if (!$canUpdate)
              return false;
            if (!empty($model->contInv->document_id))
              return false;
            $url = Url::toRoute(['invoice-detail/update', 'id' => $model->id]);

            return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', $url, [
              'title' => Yii::t('app', 'Edit')
            ]);
          },
          'delete' => function($url, $model) use ($canDelete) {
            if (!$canDelete)
              return false;
            if (!empty($model->contInv->document_id))
              return false;
            $url = Url::toRoute(['invoice-detail/delete', 'id' => $model->id]);

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
//      'cont_inv_id',
      [
        'attribute' => 'part_id',
        'value' => 'part.partinfo',
        'filter' => Html::activeDropDownList(
          $searchModel,
          'part_id',
          ArrayHelper::map(Part::find()->select(['id', 'part_no'])->all(), 'id', 'partinfo'),
          ['class' => 'form-control select2', 'prompt' => '...']
        ),
        'contentOptions' => function($model, $column) {
          return ['class' => 'td-nowrap'];
        }
      ],
      [
        'attribute' => 'qty',
        'headerOptions' => ['style' => 'width:100px;text-align: right;vertical-align:middle;'],
        'contentOptions' => ['style' => 'width:100px;text-align: right;vertical-align:middle;'],
        'content' => function($model) {
          return Helpers::formatRemoveDecimal($model->qty);
        },
      ],
      [
        'attribute' => 'price',
        'headerOptions' => ['style' => 'width:100px;text-align: right;vertical-align:middle;'],
        'contentOptions' => ['style' => 'width:100px;text-align: right;vertical-align:middle;'],
        'content' => function($model) {
          return Helpers::formatRemoveDecimal($model->price);
        },
      ],
      [
        'attribute' => 'part_order_id',
        'value' => 'partOrder.order_no',
        'filter' => Html::activeDropDownList(
          $searchModel,
          'part_order_id',
          ArrayHelper::map(
            PartOrder::find()->select(['id', 'order_no'])->all(),
            'id',
            'order_no'
          ),
          ['class' => 'form-control select2', 'prompt' => '...']
        ),
        'contentOptions' => function($model, $column) {
          return ['class' => 'td-nowrap'];
        }
      ],
      [
        'attribute' => 'contract_id',
        'value' => 'contract.contract_no',
        'filter' => Html::activeDropDownList(
          $searchModel,
          'contract_id',
          ArrayHelper::map(Contract::find()->select(['id', 'contract_no'])->all(), 'id', 'contract_no'),
          ['class' => 'form-control select2', 'prompt' => '...']
        ),
        'contentOptions' => function($model, $column) {
          return ['class' => 'td-nowrap'];
        }
      ],
      [
        'attribute' => 'remarks',
        'contentOptions' => function($model, $column) {
          return ['class' => 'td-nowrap'];
        }
      ],
    ],
  ]
);
?>
