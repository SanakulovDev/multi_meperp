<?php

use app\components\Helpers;
use app\models\Part;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

//use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\PartOrderDetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="part-order-detail-index">
  <? $parts = ArrayHelper::map(Part::find()->where(['status' => Part::STATUS_ACTIVE])->all(), 'id', 'partinfo'); ?>

  <? //php Pjax::begin(); ?>
  <?=GridView::widget(
    [
      'dataProvider' => $dataProvider,
      'filterModel' => $searchModel,
      'emptyText' => Yii::t('app', 'No results found.'),
      'summary' => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
      'options' => ['style' => 'overflow-x:scroll;clear:both'],
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
          'header' => '№',
          'headerOptions' => ['style' => 'width: 50px;text-align: center;color: #3c8dbc;'],
          'contentOptions' => ['style' => 'width: 50px;text-align: center;']
        ],
        [
          'class' => 'yii\grid\ActionColumn',
          'template' => '{update} {delete} ',
          'header' => '<i class="fa fa-fw fa-gears"></i>',
          'headerOptions' => ['style' => 'width:70px; text-align: center;vertical-align:middle;color: #3c8dbc;'],
          'contentOptions' => ['style' => 'width:70px; text-align: center;vertical-align:middle;'],
          'buttons' => [
            'update' => function ($url, $model){
              if(!empty($model->contInv->document_id))
                return false;
              $url = Url::toRoute(['part-order-detail/update', 'id' => $model->id]);
              return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', $url, [
                'title' => Yii::t('app', 'Edit')
              ]);
            },
            'delete' => function ($url, $model){
              if(!empty($model->contInv->document_id))
                return false;
              $url = Url::toRoute(['part-order-detail/delete', 'id' => $model->id]);
              return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>', $url, [
                'title' => Yii::t('app', 'Delete'),
                'data' => [
                  'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                  'method' => 'post',
                ],
              ]);
            },
          ]
        ],
        [
          'attribute' => 'part_id',
          'headerOptions' => ['style' => 'vertical-align:middle;'],
          'contentOptions' => ['style' => 'vertical-align:middle;'],
          'filter' => Html::activeDropDownList($searchModel, 'part_id', $parts, ['class' => 'form-control select2', 'prompt' => '...']),
          'content' => function ($model){
            return $model->part->partinfo;
          }
        ],
        [
          'attribute' => 'qty',
          'headerOptions' => ['style' => 'width:100px;text-align:right;vertical-align:middle;'],
          'contentOptions' => ['style' => 'width:100px;text-align:right;vertical-align:middle;'],
          'content' => function ($model){
            return Helpers::formatRemoveDecimal($model->qty, 3);
          }
        ],
        [
          'attribute' => 'price',
          'headerOptions' => ['style' => 'width:100px;text-align:right;vertical-align:middle;color: #3c8dbc;'],
          'contentOptions' => ['style' => 'width:100px;text-align:right;vertical-align:middle;'],
          'content' => function ($model){
            return Helpers::formatRemoveDecimal($model->price, 3);
          },
          'filter' => false
        ],
        [
          'attribute' => 'amount',
          'headerOptions' => ['style' => 'width:100px;text-align:right;vertical-align:middle;color: #3c8dbc;'],
          'contentOptions' => ['style' => 'width:100px;text-align:right;vertical-align:middle;'],
          'content' => function ($model){
            return Helpers::formatRemoveDecimal($model->amount, 3);
          },
          'filter' => false
        ],
        [
          'attribute' => 'exwrk_plan',
          'headerOptions' => ['style' => 'width:100px;text-align:center;vertical-align:middle;'],
          'contentOptions' => ['style' => 'width:100px;text-align:center;vertical-align:middle;'],
          'content' => function ($model){
            return (!empty($model->exwrk_plan)) ? $model->exwrk_plan : '-';
          }
        ],
        [
          'attribute' => 'exwrk_actual',
          'headerOptions' => ['style' => 'width:100px;text-align:center;vertical-align:middle;'],
          'contentOptions' => ['style' => 'width:100px;text-align:center;vertical-align:middle;'],
          'content' => function ($model){
            return (!empty($model->exwrk_actual)) ? $model->exwrk_actual : '-';
          }
        ],
        'comment'
        //				[
        //					'attribute' => 'created_at',
        //					'value' => function($data){
        //						return date('d.m.Y (H:i:s)', $data->created_at);
        //					},
        //					'headerOptions' => ['style' => 'width:200px; text-align: center;vertical-align:middle;color: #3c8dbc;'],
        //					'contentOptions' => function($model, $key, $index, $column){
        //						return [
        //							'title' => (!empty($model->createdBy)) ? $model->createdBy->username : '-',
        //							'style' => 'width:200px; text-align: center;vertical-align:middle;'
        //						];
        //					}
        //				],
        //				[
        //					'attribute' => 'updated_at',
        //					'value' => function($data){
        //						$update_val = (!empty($update_val)) ? date('d.m.Y (H:i:s)', $data->updated_at) : '-';
        //						return $update_val;
        //					},
        //					'headerOptions' => ['style' => 'width:200px; text-align: center;vertical-align:middle;color: #3c8dbc;'],
        //					'contentOptions' => function($model, $key, $index, $column){
        //						return [
        //							'style' => 'width:200px; text-align: center;vertical-align:middle;',
        //							'title' => (!empty($model->updatedBy)) ? $model->updatedBy->username : '-',
        //						];
        //					}
        //				],
      ],
    ]);?>

  <? //php Pjax::end(); ?>

</div>
