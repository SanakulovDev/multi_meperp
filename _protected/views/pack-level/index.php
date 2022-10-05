<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PackLevelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Pack level');
$this->params['breadcrumbs'][] = $this->title;
$canUpdate = Yii::$app->user->can('pack-level-update');
$canDelete = Yii::$app->user->can('pack-level-delete');
?>
<div class="pack-level-index">
    <p class="pull-right">
      <?php
      if (Yii::$app->user->can('pack-level-create'))
        echo Html::a(Yii::t('app', 'btn-create'), ['create'],
          [
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
        'filter' => Html::activeDropDownList($searchModel, 'part_id', $parts, ['class' => 'form-control select2', 'prompt' => '...']),
        'content' => function ($model) {
          return $model->part ? $model->part->partinfo : null;
        },
      ],
      [
        'attribute' => 'pack_id',
        'filter' => Html::activeDropDownList($searchModel, 'pack_id', $packs, ['class' => 'form-control select2', 'prompt' => '...']),
        'content' => function ($model) {
          return $model->pack ? $model->pack->code : null;
        },
      ],
      [
        'attribute' => 'in_pack_id',
        'filter' => Html::activeDropDownList($searchModel, 'in_pack_id', $packs, ['class' => 'form-control select2', 'prompt' => '...']),
        'content' => function ($model) {
          return $model->inPack ? $model->inPack->code : null;
        },
      ],
      'quantity',
      'level',
      [
        'attribute' => 'updated_at',
        'content' => function ($model) {
          return $model->updatedAtFormatted;
        },
      ],
    ],
  ]); ?>

  <?php Pjax::end(); ?>

</div>
