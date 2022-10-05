<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MachineSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Machine');
$this->params['breadcrumbs'][] = $this->title;
$canUpdate = Yii::$app->user->can('machine-update');
$canDelete = Yii::$app->user->can('machine-delete');
?>
<div class="machine-index">

    <p class="pull-right">
      <?php
      if (Yii::$app->user->can('machine-create'))
        echo Html::a(Yii::t('app', 'btn-create'),
          ['create'], [
            'class' => 'btn btn-success btn-sm form-modal',
            'data-intro' => Yii::t('intro', 'add-new-record')
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
        'class' => 'yii\grid\ActionColumn',
        'template' => '{update} {delete} ',
        'header' => '<i class="fa fa-fw fa-gears"></i>',
        'headerOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
        'contentOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;'],
        'buttons' => [
          'update' => function ($url, $model)use($canUpdate) {
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
          'delete' => function ($url, $model)use($canDelete) {
            if (!$canDelete) return false;
            return Html::a(
              '<span class="glyphicon glyphicon-trash"></span>',
              false,
              [
                'class' => 'modalButtonDelete',
                'data-href' => $url,
                'data-grid' => 'pjaxGrid',
                'title' => Yii::t('app', 'Delete')
              ]
            );
          }
        ],
        'visible' => $canDelete || $canUpdate
      ],
      [
        'attribute' => 'product_line_id',
        'headerOptions' => ['style' => 'width: 200px;vertical-align:middle;'],
        'contentOptions' => ['style' => 'width: 200px;vertical-align:middle;'],
        'filter' => Html::activeDropDownList($searchModel, 'product_line_id', $zones, ['class' => 'form-control select2', 'prompt' => '...']),
        'value' => 'productLine.linename'
      ],
      'no',
      'title',
      'last_count',
      [
        'filter' => Html::activeDropDownList($searchModel, 'mold_id', $molds, ['class' => 'form-control select2', 'prompt' => '...']),
        'attribute' => 'mold_id',
        'headerOptions' => ['style' => 'width: 100px;text-align: center;vertical-align:middle;'],
        'contentOptions' => ['style' => 'width: 100px;text-align: center;vertical-align:middle;'],
        'value' => 'mold.mold_no'
      ],
      'sequence',
      [
        'filter' => $searchModel->statusList,
        'attribute' => 'status',
        'headerOptions' => ['style' => 'width: 100px;text-align: center;vertical-align:middle;'],
        'contentOptions' => ['style' => 'width: 100px;text-align: center;vertical-align:middle;'],
        'content' => function ($model, $column) {
          $sts_value = $model->status;
          switch ($sts_value) {
            case 1:
              $class = 'success';
              $sts_name = "✔";
              $sts_title = Yii::t('app', 'Active');
              break;
            case 0:
              $class = 'danger';
              $sts_name = "✖";
              $sts_title = Yii::t('app', 'Inactive');
              break;
          }
          $html = Html::tag('span', Html::encode($sts_name), ['title' => $sts_title, 'class' => 'label label-' . $class]);
          return $sts_value === null ? $column->grid->emptyCell : $html;
        },
      ],
      [
        'attribute' => 'created_at',
        'value' => function ($data) {
          $create_val = ($data->created_at) ? date('d.m.Y (H:i:s)', $data->created_at) : '-';
          return $create_val;
        },
        'contentOptions' => function ($model, $key, $index, $column) {
          return [
            'style' => 'text-align: center; vertical-align:middle;',
            'title' => (!empty($model->createdBy)) ? $model->createdBy->username : '-'
          ];
        }
      ],
      [
        'attribute' => 'updated_at',
        'value' => function ($data) {
          $update_val = ($data->updated_at) ? date('d.m.Y (H:i:s)', $data->updated_at) : '-';
          return $update_val;
        },
        'contentOptions' => function ($model, $key, $index, $column) {
          return [
            'style' => 'text-align: center; vertical-align:middle;',
            'title' => (!empty($model->updatedBy)) ? $model->updatedBy->username : '-'
          ];
        }
      ],
    ],
  ]); ?>

  <?php Pjax::end(); ?>

</div>