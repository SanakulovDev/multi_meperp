<?php

use app\models\DocumentType;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\HistoryDocumentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('app', 'History documents');
$this->params['breadcrumbs'][] = $this->title;
$canView = Yii::$app->user->can('history-document-view');
?>
<div class="history-document-index">


  <?= GridView::widget(
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
          'headerOptions' => ['style' => 'width:100px;text-align: center;color: #3c8dbc;'],
          'contentOptions' => ['style' => 'width:100px;text-align: center;']
        ],
        [
          'attribute' => 'his_action',
          'headerOptions' => ['style' => 'width: 170px;text-align: center;'],
          'contentOptions' => ['style' => 'width: 170px;'],
          'content' => function ($model) {
            return $model->actionName;
          },
          'filter' => [
            'update' => Yii::t('app', 'Changing'),
            'delete' => Yii::t('app', 'Deleting'),
            'confirm' => Yii::t('app', 'Confirmation')
          ]
        ],
        [
          'attribute' => 'his_user_id',
          'headerOptions' => ['style' => 'width: 120px;text-align: center;'],
          'contentOptions' => ['style' => 'width: 120px;'],
          'content' => function ($model) {
            return $model->hisUser->fullname;
          },
        ],
        [
          'attribute' => 'his_date',
          'headerOptions' => ['style' => 'width: 150px;text-align: center;'],
          'contentOptions' => ['style' => 'width: 150px;text-align: center;'],
          'content' => function ($model) {
            return date('d.m.Y H:i', strtotime($model->his_date));
          },
        ],
        [
          'attribute' => 'document_type_id',
          'headerOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
          'contentOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
          'content' => function ($model) {
            return $model->documentType->name;
          },
          'filter' => yii\helpers\ArrayHelper::map(DocumentType::find()->all(), 'id', 'name')
        ],
        [
          'attribute' => 'docnum',
          'content' => function ($model) use ($canView) {
            return $canView ? Html::a($model->docnum, Url::toRoute(['history-document/view', 'id' => $model->id])) : $model->docnum;
          },
        ],
        [
          'attribute' => 'docdate',
          'headerOptions' => ['style' => 'width: 150px;text-align: center;'],
          'contentOptions' => ['style' => 'width: 150px;text-align: center;'],
          'content' => function ($model) {
            return date('d.m.Y', strtotime($model->docdate));
          },
        ],
        [
          'attribute' => 'status',
          'headerOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
          'contentOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
          'content' => function ($model) {
            return '<span class="btn-' . (($model->status == 1) ? 'primary' : 'warning') . ' btn-sm">
                        ' . $model->statusName . '
                    </span>';
          },
          'filter' => [0 => Yii::t('app', 'Pending'), 1 => Yii::t('app', 'Confirmed')]
        ],
      ],
    ]); ?>
</div>
