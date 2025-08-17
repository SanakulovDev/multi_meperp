<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ShipmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Shipment control');
$this->params['breadcrumbs'][] = $this->title;

$canShipmentDetailIndex = Yii::$app->user->can('shipment-detail-index');
$canDelete = Yii::$app->user->can('document-delete');

?>
<div class="shipment-index">

    <p class="pull-right">
        <? if (Yii::$app->user->can('shipment-create')) { ?>
            <?= Html::a(
                Yii::t('app', 'btn-create'),
                ['create'],
                [
                    'class' => 'btn btn-success btn-sm',
                    'data-intro' => Yii::t('intro', 'add-new-record')
                ]
            ) ?>
        <? } ?>
    </p>


    <?= GridView::widget([
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
                'headerOptions' => ['style' => 'width:50px;text-align:center;vertical-align:middle;'],
                'contentOptions' => ['style' => 'width:50px;text-align:center;vertical-align:middle;'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{delete}',
                'headerOptions' => ['style' => 'width:40px;text-align:center;vertical-align:middle;'],
                'contentOptions' => ['style' => 'width:40px;text-align:center;vertical-align:middle;'],
                'buttons' => [
                    'delete' => function ($url, $model) use ($canDelete) {
                        if (!$canDelete) return false;

                        $url = Url::toRoute(['shipment/delete', 'id' => $model->id]);
                        return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>', $url, [
                            'title' => Yii::t('app', 'Delete'),
                            'data' => [
                                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                                'method' => 'post',
                            ],
                        ]) . '&nbsp;';
                    },
                ],
                'visible' => $canDelete
            ],
            [
                'attribute' => 'title',
                'headerOptions' => ['style' => 'width:120px;text-align:left;vertical-align:middle;color:#3c8dbc;'],
                'contentOptions' => ['style' => 'width:120px;text-align:left;vertical-align:middle;'],
                'content' => function ($model) use ($canShipmentDetailIndex) {
                    if (!$canShipmentDetailIndex) return $model->title;
                    return Html::a($model->title, Url::toRoute(['shipment-detail/index', 'ShipmentDetailSearch[shipment_id]' => $model->id]));
                    
                }
            ],
            [
                'attribute' => 'days',
                'headerOptions' => ['style' => 'width:100px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
                'contentOptions' => ['style' => 'width:100px;text-align:center;vertical-align:middle;'],
                'content' => function ($model) {
                    return $model->days;
                }
            ],
            'report_date',
            'created_at'
            


        ],
    ]); ?>



</div>