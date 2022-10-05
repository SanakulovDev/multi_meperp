<?php

use app\components\Helpers;
use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ShipmentDetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Shipment control details');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Shipment control'), 'url' => ['shipment/index']];
$this->params['breadcrumbs'][] = $this->title;

$canUpdate = Yii::$app->user->can('shipment-detail-update');

?>
<div class="shipment-detail-index">
    <style>
        .table-shipment tr td,
        .table-shipment tr th {
            font-size: 12px !important;
        }

        .td-nowrap {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .ul-shipment {
            list-style-type: none;
            padding: 0px 0px 0px 7px;
            margin: 0px;
            float: left;
            border-left: 3px solid #ccc;
        }

        .ul-shipment li {
            display: inline;
            margin-right: 15px;
        }

        .modalButtonUpdate:hover {
            cursor: pointer;
        }
    </style>

    <div class="panel panel-body panel-dafault">


        <ul class="ul-shipment">
            <li><?= Yii::t('app', 'Calculation') ?>: <b><?= $shipment->title ?></b></li>
            <li><?= Yii::t('app', 'Days') ?>: <b><?= $shipment->days ?></b></li>
            <li><?= Yii::t('app', 'Report date') ?>: <b><?= $shipment->report_date ?></b></li>
            <li><?= Yii::t('app', 'Created at') ?>: <b><?= $shipment->created_at ?></b></li>
        </ul>

        <p class="pull-right" style="margin: 0px">
            <? if (Yii::$app->user->can('shipment-detail-xls')) { ?>
                <?= Html::a(
                    Yii::t('app', 'btn-download'),
                    ['xls'],
                    [
                        'class' => 'btn btn-success btn-xs searchPjax',
                        'data-intro' => Yii::t('intro', 'download')
                    ]
                ) ?>
            <? } ?>
        </p>


    </div>



    <?php Pjax::begin(['id' => 'pjaxGrid']); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'emptyText' => Yii::t('app', 'No results found.'),
        'summary' => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
        'options' => ['style' => 'overflow-x:scroll;clear:both'],
        'tableOptions' => [
            'style' => 'table-layout: fixed;',
            'class' => 'sm_filter table table-striped table-bordered table-condensed table-sm-padding_2_0 table-shipment',
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
                'headerOptions' => ['style' => 'width:20px;text-align:center;vertical-align:middle;'],
                'contentOptions' => ['style' => 'width:20px;text-align:center;vertical-align:middle;'],
            ],
            [
                'attribute' => 'part_id',
                'headerOptions' => ['style' => 'width:150px;text-align:left;vertical-align:middle;'],
                'contentOptions' => function ($model) {
                    return [
                        'style' => 'width:150px;text-align:left;vertical-align:middle;',
                        'class' => 'td-nowrap',
                        'title' => $model->part->partinfo
                    ];
                },
                'content' => function ($model) {
                    return $model->part->partinfo;
                },
                'filter' => Html::activeDropDownList($searchModel, 'part_id', $parts, ['class' => 'form-control select2', 'prompt' => '...']),
            ],
            [
                'attribute' => 'part_name',
                'headerOptions' => ['style' => 'width:150px;text-align:left;vertical-align:middle;color:#3c8dbc;'],
                'contentOptions' => function ($model) {
                    return [
                        'style' => 'width:150px;text-align:left;vertical-align:middle;',
                        'class' => 'td-nowrap',
                        'title' => $model->part->part_name
                    ];
                },
                'content' => function ($model) {
                    return $model->part->part_name;
                }
            ],
            [
                'attribute' => 'supplier_id',
                'headerOptions' => ['style' => 'width:150px;text-align:left;vertical-align:middle;'],
                'contentOptions' => function ($model) {
                    return [
                        'style' => 'width:150px;text-align:left;vertical-align:middle;',
                        'class' => 'td-nowrap',
                        'title' => $model->supplier->name ?? ''
                    ];
                },
                'content' => function ($model) {
                    return $model->supplier->name ?? '';
                },
                'filter' => Html::activeDropDownList($searchModel, 'supplier_id', $suppliers, ['class' => 'form-control select2', 'prompt' => '...']),
            ],
            [
                'attribute' => 'pack_size',
                'headerOptions' => ['style' => 'width:90px;text-align:right;vertical-align:middle;'],
                'contentOptions' => ['style' => 'width:90px;text-align:right;vertical-align:middle;'],
                'content' => function ($model) use ($canUpdate) {
                    // bu yerda agar joriy pack size dan farq qilsa update tugmasi bolishi kk

                    $currentPackSize = null;
                    if (count(ArrayHelper::map($model->part->partPackings, 'id', 'pack_qty')) != 0) {
                        $currentPackSize = Helpers::numberFormatRemoveZero(min(ArrayHelper::map($model->part->partPackings, 'id', 'pack_qty')), 2, '.', ' ');
                    }
                    $savedPackSize = Helpers::numberFormatRemoveZero($model->pack_size, 2, '.', ' ');
                    if ($savedPackSize != $currentPackSize) {
                        $content = '<b class="text-danger" title="' . $currentPackSize . '">' . $savedPackSize . '</b>';
                        if (!$canUpdate) return $content;
                        $url = Url::toRoute(['shipment-detail/recalculate', 'id' => $model->id]);
                        $button =  Html::a(
                            '<span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>',
                            $url,
                            [
                                'title' => Yii::t('app', 'Recalculate') . ' - ' . $currentPackSize
                            ]
                        );
                        if (!$canUpdate) return $content;
                        return $content . ' ' . $button;
                    }
                    return $savedPackSize;
                }
            ],
            [
                'attribute' => 'model',
                'headerOptions' => ['style' => 'width:60px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
                'contentOptions' => ['style' => 'width:60px;text-align:center;vertical-align:middle;'],
                'content' => function ($model) {
                    return $model->part->productModel->modelname ?? '';
                },
                'filter' => Html::activeDropDownList($searchModel, 'model', $models, ['class' => 'form-control', 'prompt' => '...']),
            ],
            [
                'attribute' => 'unit',
                'headerOptions' => ['style' => 'width:60px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
                'contentOptions' => ['style' => 'width:60px;text-align:center;vertical-align:middle;'],
                'content' => function ($model) {
                    return $model->part->unit->unit_value;
                },
                'filter' => Html::activeDropDownList($searchModel, 'unit', $units, ['class' => 'form-control', 'prompt' => '...']),
            ],
            [
                'attribute' => 'disruption_date',
                'headerOptions' => ['style' => 'width:100px;text-align:right;vertical-align:middle;'],
                'contentOptions' => ['style' => 'width:100px;text-align:right;vertical-align:middle;'],
                'content' => function ($model) {
                    return date('d M', strtotime($model->disruption_date));
                },
                'filter' => Html::activeDropDownList($searchModel, 'disruption_date', $dates, ['class' => 'form-control', 'prompt' => '...']),
                // 'filter' => DateTimePicker::widget([
                //     'model' => $searchModel,
                //     'attribute' => 'disruption_date',
                //     'type' => DateTimePicker::TYPE_INPUT,
	            //     'removeButton' => true,
                //     'pluginOptions' => [
                //         'language' => 'ru',
                //         'autoclose' => true,
                //         'format' => 'yyyy-mm-dd',
                //         'minView' => 'month',
                //         'maxView' => 'month',
                //     ],
                //     'options' => [
                //         'autocomplete' => 'off'
                //     ]
                // ]),

                'format' => 'html',
            ],
            [
                'attribute' => 'need_qty',
                'headerOptions' => ['style' => 'width:90px;text-align:right;vertical-align:middle;'],
                'contentOptions' => function ($model) {
                    return [
                        'style' => 'width:90px;text-align:right;vertical-align:middle;',
                        'title' => Helpers::numberFormatRemoveZero($model->coverage_qty, 2, '.', ' ')
                    ];
                },
                'content' => function ($model) {
                    return Helpers::numberFormatRemoveZero($model->need_qty, 2, '.', ' ');
                }
            ],
            [
                'attribute' => 'ready_qty',
                'headerOptions' => ['style' => 'width:90px;text-align:right;vertical-align:middle;'],
                'contentOptions' => ['style' => 'width:90px;text-align:right;vertical-align:middle;'],
                'content' => function ($model) use ($canUpdate) {
                    $content = Helpers::numberFormatRemoveZero($model->ready_qty, 2, '.', ' ');
                    if (!$canUpdate) return $content;
                    $url = Url::toRoute(['shipment-detail/update', 'id' => $model->id]);
                    return Html::a(
                        $content,
                        false,
                        [
                            'class' => 'modalButtonUpdate',
                            'value' => $url,
                            'title' => Yii::t('app', 'Update')
                        ]
                    );
                }
            ],
            [
                'attribute' => 'diff_ready_need',
                'headerOptions' => ['style' => 'width:90px;text-align:right;vertical-align:middle;color:#3c8dbc;'],
                'contentOptions' => ['style' => 'width:90px;text-align:right;vertical-align:middle;'],
                'content' => function ($model) {
                    return Helpers::numberFormatRemoveZero($model->diffReadyNeed ?? 0, 2, '.', ' ');
                }
            ],
            [
                'attribute' => 'approved_qty',
                'headerOptions' => ['style' => 'width:90px;text-align:right;vertical-align:middle;'],
                'contentOptions' => ['style' => 'width:90px;text-align:right;vertical-align:middle;'],
                'content' => function ($model) use ($canUpdate) {
                    $content = Helpers::numberFormatRemoveZero($model->approved_qty, 2, '.', ' ');
                    if (!$canUpdate) return $content;
                    $url = Url::toRoute(['shipment-detail/update', 'id' => $model->id]);
                    return Html::a(
                        $content,
                        false,
                        [
                            'class' => 'modalButtonUpdate',
                            'value' => $url,
                            'title' => Yii::t('app', 'Update')
                        ]
                    );
                }
            ],
            [
                'attribute' => 'diff_appr_ready',
                'headerOptions' => ['style' => 'width:90px;text-align:right;vertical-align:middle;color:#3c8dbc;'],
                'contentOptions' => ['style' => 'width:90px;text-align:right;vertical-align:middle;'],
                'content' => function ($model) {
                    return Helpers::numberFormatRemoveZero($model->diffApprReady ?? 0, 2, '.', ' ');
                }
            ],
            [
                'attribute' => 'comment',
                'headerOptions' => ['style' => 'text-align:left;vertical-align:middle;'],
                'contentOptions' => function ($model) {
                    return [
                        'style' => 'width:150px;text-align:left;vertical-align:middle;',
                        'class' => 'td-nowrap',
                        'title' => $model->comment
                    ];
                },
                'content' => function ($model) use ($canUpdate) {
                    $content = ($model->comment) ? $model->comment : '[' . Yii::t('app', 'no comment') . ']';
                    if (!$canUpdate) return $content;
                    $url = Url::toRoute(['shipment-detail/update', 'id' => $model->id]);
                    return Html::a(
                        $content,
                        false,
                        [
                            'class' => 'modalButtonUpdate',
                            'value' => $url
                        ]
                    );
                }
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>