<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $parts */
/* @var $suppliers */
/* @var $searchModel app\models\PackSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('app', 'Container');
$this->params['breadcrumbs'][] = $this->title;
$canUpdate = Yii::$app->user->can('pack-update');
$canDelete = Yii::$app->user->can('pack-delete');
?>
<div class="pack-index">

    <p class="pull-right">
      <?php
      if (Yii::$app->user->can('pack-create'))
        echo Html::a(Yii::t('app', 'btn-create'), ['create'],
          [
            'class' => 'btn btn-success btn-sm form-modal',
            'style' => 'margin-right: 5px',
            'data-intro' => Yii::t('intro', 'add-new-record')
          ]);
      if (Yii::$app->user->can('pack-xls'))
        echo Html::a(Yii::t('app', 'btn-download'), ['xls', 'PackSearch' => ($_GET['PackSearch'] ?? null)],
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
      'code',
      'description',
      'construction',
      'length',
      'width',
      'height',
      'weight',
      'thickness',
      'level',
      'quantity',
    ],
  ]); ?>
  <?php Pjax::end(); ?>

</div>
