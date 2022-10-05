<?php

use app\assets\AdminLteAsset;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\JqueryAsset;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\rbac\models\AuthItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Roles and permissions');
$this->params['breadcrumbs'][] = $this->title;

$canUpdate = Yii::$app->user->can('auth-item-update');
$canDelete = Yii::$app->user->can('auth-item-delete');
$canCreate = Yii::$app->user->can('auth-item-create');
$canDownload = Yii::$app->user->can('auth-item-xls');


$this->registerCssFile("@themes/css/jquery.transfer.css?v=0.0.2", ['depends' => [AdminLteAsset::className()]]);
$this->registerJsFile("@themes/js/jquery.transfer.js?v=0.0.4", ['depends' => [JqueryAsset::className()]]);
?>
<div class="auth-item-index">
    <p class="pull-right">
      <?php
      if ($canCreate)
        echo Html::a(Yii::t('app', 'btn-create'), ['create'],
          [
            'class' => 'btn btn-success btn-sm form-modal',
            'data-intro' => Yii::t('intro', 'add-new-record')
          ]);
      if ($canDownload)
        echo Html::a(Yii::t('app', 'btn-download'), ['xls', 'PackSearch' => ($_GET['PackSearch'] ?? null)],
          [
            'class' => 'btn btn-info btn-sm',
            'style' => 'margin-left: 5px',
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
        'template' => '{create} {update} {delete} ',
        'header' => '<i class="fa fa-fw fa-gears"></i>',
        'headerOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
        'contentOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;'],
        'buttons' => [

        	'create' => function ($url, $model) use ($canCreate) {
            if (!$canCreate) return false;
            return Html::a(
              '<span  class="glyphicon glyphicon-copy"></span>',
              false,
              [
                'class' => 'modalButtonUpdate text-info',
                'value' => $url,
                'title' => Yii::t('app', 'Copy')
              ]
            );
          },

          'update' => function ($url, $model) use ($canUpdate) {
            if (!$canUpdate) return false;
            return Html::a(
              '<span  class="glyphicon glyphicon-pencil"></span>',
              false,
              [
                'class' => 'modalButtonUpdate ',
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
//                'style' => 'color:red',
                'class' => 'modalButtonDelete text-danger',
                'data-href' => $url,
                'data-grid' => 'pjaxGrid',
                'title' => Yii::t('app', 'Delete')
              ]);
          }
        ],
        'visible' => $canDelete || $canUpdate
      ],

      'name',
      'description:ntext',
//      'rule_name',
//      'data:ntext',
      [
        'attribute' => 'created_at',
        'value' => 'createdAtFormatted'
      ],
      [
        'attribute' => 'updated_at',
        'value' => 'updatedAtFormatted'
      ],
    ],
  ]); ?>

  <?php Pjax::end(); ?>

</div>
