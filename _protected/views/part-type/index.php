<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PartTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('app', 'The types of part');
$this->params['breadcrumbs'][] = $this->title;

$canUpdate = Yii::$app->user->can('part-type-update');
$canDelete = Yii::$app->user->can('part-type-delete');

?>
<div class="part-type-index">

    <p class="pull-right">
      <?php if (Yii::$app->user->can('part-type-create'))
        echo Html::a(Yii::t('app', 'btn-create'),
          ['create'], [
            'class' => 'btn btn-success btn-sm',
            'data-intro' => Yii::t('intro', 'add-new-record')
          ]); ?>
    </p>
  <?php Pjax::begin(); ?>
  <?= GridView::widget(
    [
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
        [
          'class' => 'yii\grid\SerialColumn',
          'headerOptions' => ['style' => 'width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
          'contentOptions' => ['style' => 'width:50px;text-align:center;vertical-align:middle;'],
        ],
        [
          'class' => 'yii\grid\ActionColumn',
          'template' => '{update} {delete} ',
          'header' => '<i class="fa fa-fw fa-gears"></i>',
          'headerOptions' => ['style' => 'width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
          'contentOptions' => ['style' => 'width:50px;text-align:center;vertical-align:middle;'],
          'buttons' => [
            'update' => function ($url, $model) use ($canUpdate) {
              if (!$canUpdate) return false;
              $url = Url::toRoute(['part-type/update', 'id' => $model->id]);
              return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', $url, [
                  'title' => Yii::t('app', 'Edit')
                ]) . '&nbsp;';
            },
            'delete' => function ($url, $model) use ($canDelete) {
              if (!$canDelete) return false;
              $url = Url::toRoute(['part-type/delete', 'id' => $model->id]);
              return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>', $url, [
                  'title' => Yii::t('app', 'Delete'),
                  'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                  ],
                ]) . '&nbsp;';
            },
          ],
          'visible' => $canUpdate || $canDelete
        ],
        'typename',
        'description'
      ],
    ]); ?>
  <?php Pjax::end(); ?>
</div>
