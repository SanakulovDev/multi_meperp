<?php

use app\components\Helpers;
use app\models\ConsolidationType;
use app\models\ContractSource;
use app\models\ShipMode;
use Codeception\Lib\Generator\Helper;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MfuSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('app', 'MFU');
$this->params['breadcrumbs'][] = $this->title;
$canUpdate = Yii::$app->user->can('mfu-update');
$canDelete = Yii::$app->user->can('mfu-delete');
$canView = Yii::$app->user->can('mfu-view');
?>
<div class="mfu-index">

    <p class="pull-right">
      <?php
      if (Yii::$app->user->can('mfu-create'))
        echo Html::a(Yii::t('app', 'btn-create'), ['create'],
          [
            'class' => 'btn btn-success btn-sm',
            'data-step' => 2, 'data-intro' => Yii::t('intro', 'add-new-record')
          ]);
      ?>
    </p>

  <?=
  GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'emptyText' => Yii::t('app', 'No results found.'),
    'summary' => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
    'options' => ['style' => 'overflow-x:scroll;clear:both'],
    'tableOptions' => [
      'style' => 'table-layout: fixed;',
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
      [
        'class' => 'yii\grid\SerialColumn',
        'header' => '№',
        'headerOptions' => ['style' => 'width: 40px;text-align: center;color: #3c8dbc;'],
        'contentOptions' => ['style' => 'width: 40px;text-align: center;']
      ],
      [
        'class' => 'yii\grid\ActionColumn',
        'header' => '<i class="fa fa-fw fa-gears"></i>',
        'headerOptions' => ['style' => 'width: 70px;text-align: center;vertical-align:middle;color: #3c8dbc;'],
        'contentOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;'],

        'buttons' => [
          'view' => function ($url, $model) use ($canView) {
            if (!$canView) return false;
            return Html::a(
              '<span  class="glyphicon glyphicon-eye-open"></span>',
              $url,
              [
                'title' => Yii::t('app', 'View')
              ]
            );
          },
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
        'visible' => $canDelete || $canUpdate || $canView,
      ],
      [
        'attribute' => 'part_id',
        'label' => 'Part DY(KD)',
        'headerOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
        'contentOptions' => ['style' => 'width: 150px;text-align: left;vertical-align:middle;'],
        'content' => function ($model) {
          return $model->part->partinfo;
        },
      ],
      [
        'attribute' => 'ship_mode_id',
        'headerOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
        'contentOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
        'content' => function ($model) {
          return $model->shipMode->name ?? '';
        },
        'filter' => ArrayHelper::map(ShipMode::find()->all(), 'id', 'name')
      ], 
      [
        'attribute' => 'mfu_code',
        'headerOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
        'contentOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
      ],
      [
        'attribute' => 'contract_source_id',
        'headerOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
        'contentOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
        'content' => function ($model) {
          return $model->contractSource->name ?? '';
        },
        'filter' => ArrayHelper::map(ContractSource::find()->all(), 'id', 'name')
      ],
      [
        'attribute' => 'constraint',
        'headerOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
        'contentOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
        'content' => function ($model) {
          return ($model->constraint == 1) ? Yii::t('app', 'YES') : Yii::t('app', 'NO');
        },
        'filter' => [1 => Yii::t('app', 'YES'), 0 => Yii::t('app', 'NO')]
      ],
      [
        'attribute' => 'consolidation_type_id',
        'headerOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
        'contentOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
        'content' => function ($model) {
          return $model->consolidationType ? $model->consolidationType->name : '';
        },
        'filter' => ArrayHelper::map(ConsolidationType::find()->all(), 'id', 'name')
      ],
      [
        'attribute' => 'moq',
        'headerOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
        'contentOptions' => ['style' => 'width: 150px;text-align: right;vertical-align:middle;'],
        'content' => function ($model) {
          return Helpers::numberFormatRemoveZero($model->moq,0);
        },
      ],
      [
        'attribute' => 'capacity',
        'headerOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
        'contentOptions' => ['style' => 'width: 150px;text-align: right;vertical-align:middle;'],
        'content' => function ($model) {
          return Helpers::numberFormatRemoveZero($model->capacity,0);
        },
      ],
      [
        'attribute' => 'bank',
        'headerOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
        'contentOptions' => ['style' => 'width: 150px;text-align: right;vertical-align:middle;'],
        'content' => function ($model) {
          return Helpers::numberFormatRemoveZero($model->bank,0);
        },
      ],
    ],
  ]);
  ?>
</div>
