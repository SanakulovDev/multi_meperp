<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductModelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('app', 'OEM models');
$this->params['breadcrumbs'][] = $this->title;
$canUpdate = Yii::$app->user->can('product-model-update');
$canDelete = Yii::$app->user->can('product-model-delete');
$canCreate = Yii::$app->user->can('product-model-create');
?>
<div class="product-model-index">
  <p class="pull-right">
    <? if ($canCreate) { ?>
      <?=
      Html::a(Yii::t('app', 'btn-create'), ['create'], [
          'class' => 'btn btn-success btn-sm',
          'data-intro' => Yii::t('intro', 'add-new-record')
        ]
      )
      ?>
    <? } ?>
  </p>
  <?php Pjax::begin(); ?>
  <?=GridView::widget(
    [
      'dataProvider' => $dataProvider,
      'filterModel' => $searchModel,
      'summary' => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
      'rowOptions' => ['style' => 'white-space:nowrap; vertical-align:middle;'],
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
          'headerOptions' => ['style' => 'text-align: center;color: #3c8dbc;'],
          'contentOptions' => ['style' => 'text-align: center;']
        ],
        [
          'class' => 'yii\grid\ActionColumn',
          'template' => '{update} {delete} ',
          'header' => '<i class="fa fa-fw fa-gears"></i>',
          'headerOptions' => ['style' => 'width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
          'contentOptions' => ['style' => 'width:50px;text-align:center;vertical-align:middle;'],
          'buttons' => [
            'update' => function($url, $model) use ($canUpdate) {
              if (!$canUpdate)
                return false;
              $url = Url::toRoute(['product-model/update', 'id' => $model->id]);

              return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', $url, [
                  'title' => Yii::t('app', 'Edit')
                ]).'&nbsp;';
            },
            'delete' => function($url, $model) use ($canDelete) {
              if (!$canDelete)
                return false;
              $url = Url::toRoute(['product-model/delete', 'id' => $model->id]);

              return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>', $url, [
                  'title' => Yii::t('app', 'Delete'),
                  'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                  ],
                ]).'&nbsp;';
            },
          ],
          'visible' => $canUpdate || $canDelete
        ],
        'modelname',
        'description',
        [
          'filter' => $searchModel->is_vehicle,
          'attribute' => 'is_vehicle',
          'contentOptions' => ['style' => 'text-align: center;'],
          'content' => function ($model, $column) {
            switch ($model->is_vehicle) {
              case 1:
                $class = 'success';
                $sts_name = "✔";
                break;
              case 0:
                $class = 'danger';
                $sts_name = "✖";
                break;
	            default: $sts_name = '-';
                $class = 'danger';
                break;
            }
            return $html = Html::tag('span', Html::encode($sts_name), ['class' => 'label label-' . $class]);
          },
        ],



      ],
    ]);
  ?>
  <?php Pjax::end(); ?>
</div>
