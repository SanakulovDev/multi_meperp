<?php

use app\helpers\CssHelper;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;
$canView = Yii::$app->user->can('user-view');
$canDelete = Yii::$app->user->can('user-delete');
$canCreate =  Yii::$app->user->can('user-create');
$canXls = Yii::$app->user->can('user-xls');
?>
<div class="user-index">

    <p class="pull-right">
		<? if ($canCreate) { ?>
      <?= Html::a(Yii::t('app', 'btn-create'), ['create'],
        [
          'class' => 'btn btn-success btn-sm',
          'data-step' => 2, 'data-intro' => Yii::t('intro', 'add-new-record')
        ]) ?>
        <? } ?>
		<? if ($canXls) { ?>
      <?= Html::a(Yii::t('app', 'btn-download'), ['xls', 'UserSearch' => ($_GET['UserSearch'] ?? null)],
        [
          'class' => 'btn btn-info btn-sm',
          'data-step' => 3,
          'data-intro' => Yii::t('intro', 'download-button')
        ]) ?>
        <? } ?>
    </p>
  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'emptyText' => Yii::t('app', 'No results found.'),
    'summary' => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
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
    'options' => ['style' => 'overflow:auto;clear:both'],
    'columns' => [
      [
        'class' => 'yii\grid\SerialColumn',
        'headerOptions' => ['style' => 'width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
        'contentOptions' => ['style' => 'width:50px;text-align:center;vertical-align:middle;'],
      ],
      [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{view} {delete}',
        'header' => '<i class="fa fa-fw fa-gears"></i>',
        'headerOptions' => ['style' => 'width:70px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
        'contentOptions' => ['style' => 'width:70px;text-align:center;vertical-align:middle;'],
        'buttons' => [
          'view' => function ($url, $model, $key) use($canView) {
            if(!$canView) return false;
            $url = Url::toRoute(['user/view', 'id' => $model->id]);
            return Html::a('', $url, ['title' => 'View user', 'class' => 'glyphicon glyphicon-eye-open']);
          },
          'delete' => function ($url, $model, $key) use($canDelete)  {
            if(!$canDelete) return false;
            $url = Url::toRoute(['user/delete', 'id' => $model->id]);
            return Html::a('', $url,
              ['title' => 'Delete user',
                'class' => 'glyphicon glyphicon-trash',
                'data' => [
                  'confirm' => Yii::t('app', 'Are you sure you want to delete this user?'),
                  'method' => 'post'
                ]
              ]);
          }
        ]
      ],
      [
        'attribute' => 'username',
        'headerOptions' => ['style' => 'width: 200px;vertical-align:middle;'],
        'contentOptions' => ['style' => 'width: 200px;vertical-align:middle;'],
        'content' => function ($model) {
          return Html::a($model->username, Url::toRoute(['user/update', 'id' => $model->id]));
        },
      ],
      [
        'attribute' => 'fullname',
        'headerOptions' => ['style' => 'text-align: left;vertical-align:middle;'],
        'contentOptions' => ['style' => 'text-align: left;vertical-align:middle;'],
      ],
      [
        'attribute' => 'password_plain',
        'label' => Yii::t('app', 'Password'),
        'headerOptions' => ['style' => 'vertical-align:middle;'],
        'contentOptions' => ['style' => 'vertical-align:middle;'],
        'value' => function ($model) {
          return $model->displayPassword;
        }
      ],
      [
        'attribute' => 'status',
        'headerOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
        'filter' => $searchModel->statusList,
        'value' => function ($data) {
          return $data->getStatusName($data->status);
        },
        'contentOptions' => function ($model, $key, $index, $column) {
          return [
            'class' => CssHelper::userStatusCss($model->status),
            'style' => 'width: 150px;text-align: center;vertical-align:middle;'
          ];
        }
      ],
      [
        'attribute' => 'item_name',
        'headerOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
        'contentOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
        'filter' => $searchModel->rolesList,
        'content' => function ($data) {
          $color = 'info';
          if($data->roleName == 'admin' or $data->roleName == 'superadmin') $color = 'danger';
          return '<span class="text-' . $color . ' boolen-true" style="font-weight:bold;">' . $data->roleName . '</span>';
        },
      ],
    ], // columns
  ]); ?>

</div>
