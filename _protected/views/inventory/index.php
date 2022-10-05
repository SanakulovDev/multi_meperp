<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ApiSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('app', 'Physical Inventory');
$this->params['breadcrumbs'][] = $this->title;
$canUpdate = Yii::$app->user->can('inventory-update');
$canDelete = Yii::$app->user->can('inventory-delete');
?>
<div class="api-index">

    <p class="pull-right">
      <?php
      if (Yii::$app->user->can('inventory-create'))
        echo Html::a(Yii::t('app', 'btn-create'), ['create'], [
          'class' => 'btn btn-success btn-sm',
          'style' => 'margin-right: 5px',
          'data-intro' => Yii::t('intro', 'add-new-record')
        ]);
      if (Yii::$app->user->can('inventory-xls'))
        echo Html::a(Yii::t('app', 'btn-download'), ['xls', 'ApiSearch' => ($_GET['ApiSearch'] ?? null)], [
          'class' => 'btn btn-info btn-sm',
          'data-intro' => Yii::t('intro', 'download-button')
        ]);
      ?>
    </p>

  <?=
  GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'emptyText' => Yii::t('app', 'No results found.'),
    'tableOptions' => [
      'class' => 'sm_filter table table-striped table-bordered table-condensed table-sm-padding_2_0',
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
    'summary' => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
    'options' => ['style' => 'overflow-x:scroll;clear:both'],
    'columns' => [
      [
        'class' => 'yii\grid\SerialColumn',
        'header' => '№',
        'headerOptions' => ['style' => 'width: 40px;text-align: center;color: #3c8dbc;'],
        'contentOptions' => ['style' => 'width: 40px;text-align: center;']
      ],
      [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{update} {delete} ',
        'header' => '<i class="fa fa-fw fa-gears"></i>',
        'headerOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
        'contentOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;'],

        'buttons' => [
          'update' => function ($url, $model) use ($canUpdate) {
            if (!$canUpdate) return false;
            return Html::a(
              '<span  class="glyphicon glyphicon-pencil"></span>',
              $url,
              [
                'title' => Yii::t('app', 'Update')
              ]
            );
          },
          'delete' => function ($url, $model) use ($canDelete) {
            if (!$canDelete) return false;
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
        'visible' => $canDelete || $canUpdate,
      ],
      [
        'attribute' => 'id',
        'headerOptions' => ['style' => 'width:100px;text-align: center;vertical-align:middle;'],
        'contentOptions' => ['style' => 'width:100px;text-align: center;vertical-align:middle;'],
      ],
      [
        'attribute' => 'inventory_date',
        'headerOptions' => ['style' => 'text-align: center;vertical-align:middle;'],
        'contentOptions' => ['style' => 'text-align: center;vertical-align:middle;'],
      ],
      [
        'attribute' => 'stock_date',
        'headerOptions' => ['style' => 'text-align: center;vertical-align:middle;'],
        'contentOptions' => ['style' => 'text-align: center;vertical-align:middle;'],
      ],
      [
        'attribute' => 'created_by',
        'headerOptions' => ['style' => 'text-align: center;vertical-align:middle;'],
        'contentOptions' => ['style' => 'text-align: center;vertical-align:middle;'],
        'value' => function ($model) {
          return $model->createdBy->fullname;
        },
      ],
      [
        'attribute' => 'created_at',
        'headerOptions' => ['style' => 'text-align: center;vertical-align:middle;'],
        'contentOptions' => ['style' => 'text-align: center;vertical-align:middle;'],
        'value' => function ($model) {
          return date('d.m.Y H:i', $model->created_at);
        },
      ],
    ],
  ]);
  ?>


</div>
