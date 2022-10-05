<?php
use app\components\Helpers;
use app\models\FgInvoiceDetail;
use app\models\Unit;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$canUpdate = Yii::$app->user->can('fg-invoice-detail-update');
$canDelete = Yii::$app->user->can('fg-invoice-detail-delete');
?>
<?=GridView::widget(
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
        'headerOptions' => ['style' => 'width: auto;text-align: center;color: #3c8dbc;'],
        'contentOptions' => ['style' => 'width: auto;text-align: center;']
      ],
      [
        'controller' => 'invoice-detail',
        'class' => 'yii\grid\ActionColumn',
        'template' => '{update}&nbsp;{delete} ',
        'header' => '<i class="fa fa-fw fa-gears"></i>',
        'headerOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
        'contentOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;'],
        'buttons' => [
          'update' => function($url, $model) use ($canUpdate) {
            if(!$canUpdate) return false;
            if(!empty($model->contInv->document_id)) return false;
            $url = Url::toRoute(['fg-invoice-detail/update', 'id' => $model->id]);

            return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', $url, [
              'title' => Yii::t('app', 'Edit')
            ]);
          },
          'delete' => function($url, $model) use ($canDelete) {
            if(!$canDelete) return false;
            if(!empty($model->contInv->document_id)) return false;
            $url = Url::toRoute(['fg-invoice-detail/delete', 'id' => $model->id]);

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
        'attribute' => 'part_no',
        'contentOptions' => function($model, $key, $index, $column) {
          return ['title' => FgInvoiceDetail::getSourceText($model->source)];
        }
      ],
      'part_name',
      [
        'attribute' => 'qty',
        'contentOptions' => ['style' => 'text-align: right;vertical-align:middle;'],
        'content' => function($model) {
          return Helpers::numberFormatRemoveZero($model->qty, 10);
        },
      ],
      [
        'attribute' => 'price',
        'contentOptions' => ['style' => 'text-align: right;vertical-align:middle;'],
        'content' => function($model) {
          return Helpers::numberFormatRemoveZero($model->price, 10);
        },
      ],
      [
        'attribute' => 'unit_id',
        'headerOptions' => ['style' => 'width: 80px;text-align: center;vertical-align:middle;color: #3c8dbc;'],
        'contentOptions' => ['style' => 'width: 80px;text-align: center;vertical-align:middle;'],
        'content' => function($model) {
          return $model->unit->unit_value;
        },
        'filter' => yii\helpers\ArrayHelper::map(Unit::find()->all(), 'id', 'unit_value')
      ],
      [
        'attribute' => 'created_by',
        'value' => function($data) {
          $created_val = (!empty($data->createdBy)) ? $data->createdBy->username : '-';

          return $created_val;
        },
        'contentOptions' => function($model, $key, $index, $column) {
          $content_with_title = (!empty($model->createdBy)) ? ['style' => 'text-align: center;', 'title' => date('d.m.Y (H:i:s)', $model->created_at)] : ['style' => 'text-align: center;'];

          return $content_with_title;
        }
      ],
      [
        'attribute' => 'updated_by',
        'value' => function($data) {
          $updated_val = (!empty($data->updatedBy)) ? $data->updatedBy->username : '-';

          return $updated_val;
        },
        'contentOptions' => function($model, $key, $index, $column) {
          $content_with_title = (!empty($model->updatedBy)) ? ['style' => 'text-align: center;', 'title' => date('d.m.Y (H:i:s)', $model->updated_at)] : ['style' => 'text-align: center;'];

          return $content_with_title;
        }
      ]
    ],
  ]);?>
