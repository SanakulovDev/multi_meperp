<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\LmsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('app', 'Storage');
$this->params['breadcrumbs'][] = $this->title;
$canUpdate = Yii::$app->user->can('lms-update');
$canDelete = Yii::$app->user->can('lms-delete');
?>
<div class="lms-index">

    <p class="pull-right">
      <?php
      if (Yii::$app->user->can('lms-create'))
        echo Html::a(Yii::t('app', 'btn-create'), ['create'],
          [
            'class' => 'btn btn-success btn-sm form-modal',
            'style' => 'margin-right: 5px',
            'data-intro' => Yii::t('intro', 'add-new-record')
          ]);
      if (Yii::$app->user->can('lms-xls'))
        echo Html::a(Yii::t('app', 'btn-download'), ['xls', 'LmsSearch' => ($_GET['LmsSearch'] ?? null)],
          [
            'class' => 'btn btn-info btn-sm',
            'data-intro' => Yii::t('intro', 'download-button')
          ]);
      ?>
    </p>

  <?php Pjax::begin(['id' => 'pjaxGrid']); ?>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'summary' => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
    'options' => ['style' => 'overflow:auto;clear:both'],
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
    'columns' => [
      ['class' => 'yii\grid\SerialColumn'],
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
              false,
              [
                'class' => 'modalButtonUpdate',
                'value' => $url,
                'title' => Yii::t('app', 'Update')
              ]
            );
          },
          'delete' => function ($url, $model) use ($canDelete) {
            if (!$canDelete) return false;
            return Html::a('<span class="glyphicon glyphicon-trash"></span>',
              false,
              [
                'class' => 'modalButtonDelete',
                'data-href' => $url,
                'data-grid' => 'pjaxGrid',
                'title' => Yii::t('app', 'Delete')
              ]);
          }
        ],
        'visible' => $canDelete || $canUpdate
      ],
      [
        'attribute' => 'part_id',
        'headerOptions' => ['style' => 'width: 170px;text-align: center;vertical-align:middle;'],
        'contentOptions' => ['style' => 'width: 170px;text-align: center;vertical-align:middle;'],
        'filter' => Html::activeDropDownList($searchModel, 'part_id', $parts, ['class' => 'form-control select2', 'prompt' => '...']),
        'content' => function ($model) {
          return $model->part->partinfo;
        }
      ],
      [
        'attribute' => 'supplier_id',
        'headerOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
        'contentOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
        'filter' => Html::activeDropDownList($searchModel, 'supplier_id', $suppliers, ['class' => 'form-control select2', 'prompt' => '...']),
        'content' => function ($model) {
          return $model->supplier ? $model->supplier->name : '';
        }
      ],
      [
        'attribute' => 'warehouse_id',
        'headerOptions' => ['style' => 'width: 100px;text-align: center;vertical-align:middle;'],
        'contentOptions' => ['style' => 'width: 100px;text-align: center;vertical-align:middle;'],
        'filter' => Html::activeDropDownList($searchModel, 'warehouse_id', $warehouses, ['class' => 'form-control select2', 'prompt' => '...']),
        'content' => function ($model) {
          return $model->warehouse->name;
        }
      ],
      'dloc',
      'minimum',
      'maximum',
      'stack',
      [
        'attribute' => 'bms',
        'filter' => Html::activeDropDownList($searchModel, 'bms', $searchModel->getSizeList(), ['class' => 'form-control', 'prompt' => '...']),
        'content' => function ($model) {
          return $model->getSizeList()[$model->bms]; // $model->highTheftFormatted;
        }
      ],
      'mpr',
      [
        'attribute' => 'high_theft',
        'filter' => Html::activeDropDownList($searchModel, 'high_theft', $searchModel->highTheftList(), ['class' => 'form-control', 'prompt' => '...']),
        'content' => function ($model) {
          return $model->highTheftFormatted;
        }
      ],
    ],
  ]); ?>
  <?php Pjax::end(); ?>

</div>
