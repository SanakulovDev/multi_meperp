<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\WaybillSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('app', 'Waybills(FG Invoice)');
$this->params['breadcrumbs'][] = $this->title;
$canView = Yii::$app->user->can('waybill-view');
$canUpdate = Yii::$app->user->can('waybill-update');
$canDelete = Yii::$app->user->can('waybill-delete');
$canCreate =  Yii::$app->user->can('waybill-create');
$canXls = Yii::$app->user->can('waybill-xls');

?>
<div class="waybill-index">

    <p class="pull-right">
		<? if ($canCreate) { ?>
        <?= Html::a(Yii::t('app', 'btn-create'), ['create'],
            [
                'class' => 'btn btn-success btn-sm',
                'data-intro' => Yii::t('intro', 'add-new-record')
            ])
        ?>
        <? } ?>
		<? if ($canXls) { ?>
        <?= Html::a(Yii::t('app', 'btn-download'), ['xls', 'FactorySearch' => ($_GET['FactorySearch'] ?? null)],
            [
                'class' => 'btn btn-info btn-sm',
                'data-intro' => Yii::t('intro', 'download-button')
            ])
        ?>
        <? } ?>
    </p>

    <?= GridView::widget([
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
                'headerOptions' => ['style' => 'width:50px;text-align: center;color: #3c8dbc;'],
                'contentOptions' => ['style' => 'width:50px;text-align: center;']
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '<i class="fa fa-fw fa-gears"></i>',
                'headerOptions' => ['style' => 'width:80px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
                'contentOptions' => ['style' => 'width:80px;text-align:center;vertical-align:middle;'],
                'visible' => (Yii::$app->user->can('sales') || Yii::$app->user->can('shipper')),
                'template' => '{view} {delete} &nbsp;{update}',
                'buttons' => [
                    'view' => function ($url, $model) use($canView) {
                        if(!$canView) return false;
                        $url = Url::toRoute(['waybill/view', 'id' => $model->id]);
                        return Html::a('<span class="glyphicon  glyphicon-eye-open" aria-hidden="true"></span>', $url, [
                            'title' => Yii::t('app', 'Show')
                        ]) . '&nbsp;';
                    },
                    'delete' => function ($url, $model) use($canDelete)  {
                        if(!$canDelete) return false;
                            $icon_style = 'text-danger';
                            $title = Yii::t('app', 'Delete');
                            $url = Url::toRoute(['waybill/delete', 'id' => $model->id]);
                            return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>', $url, [
                                'title' => Yii::t('app', 'Delete'),
                                'class' => $icon_style,
                                'data-pjax'=>'0',
                                'data-confirm'=>Yii::t('app','Are you sure you want to delete this item?'),
                                'data-method'=>'post'
                            ]);
                    },
                    'update' => function ($url, $model) use($canUpdate)  {
                        if(!$canUpdate) return false;
                            $icon_style = 'text-warning';
                            $title = Yii::t('app', 'Update');
                            $url = Url::toRoute(['waybill/update', 'id' => $model->id]);
                            return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', $url, [
                                'title' => Yii::t('app', 'Update'),
                                'class' => $icon_style
                            ]);
                    },
                ],
                'visible' => $canView || $canUpdate || $canDelete
            ],
            'waybill_no',
            [
                'attribute' => 'invoices',
                'headerOptions' => ['style' => 'width:600px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
                'contentOptions' => ['style' => 'width:600px;text-align:left;vertical-align:middle;'],
                'value' => function ($model) use ($invoices) {
                    return  (is_array($invoices[$model->id])) ? implode(', ',$invoices[$model->id]) : '-';
                }
            ],
            [
                'attribute' => 'factory_id',
                'headerOptions' => ['style' => 'width:200px;text-align:center;vertical-align:middle;'],
                'contentOptions' => ['style' => 'width:200px;text-align:center;vertical-align:middle;'],
                'value' => function ($model) {
                    return $model->factory->name ?? '-';
                }
            ],
            [
                'attribute' => 'waybill_date',
                'headerOptions' => ['style' => 'width:200px;text-align:center;vertical-align:middle;'],
                'contentOptions' => ['style' => 'width:200px;text-align:center;vertical-align:middle;'],
            ],
            [
                'attribute' => 'created_by',
                'headerOptions' => ['style' => 'width:200px;text-align:center;vertical-align:middle;'],
                'contentOptions' => ['style' => 'width:200px;text-align:center;vertical-align:middle;'],
                'value' => function ($item) {
                    return $item->createdBy ? $item->createdBy->fullname : null;
                }
            ],
            [
                'attribute' => 'updated_at',
                'headerOptions' => ['style' => 'width:200px;text-align:center;vertical-align:middle;'],
                'contentOptions' => ['style' => 'width:200px;text-align:center;vertical-align:middle;'],
                'value' => function ($item) {
                    return $item->updatedAtFormatted;
                }
            ],
        ],
    ]); ?>


</div>
