<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\LocationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Location');
$this->params['breadcrumbs'][] = $this->title;
$canUpdate = Yii::$app->user->can('location-update');
$canDelete =  Yii::$app->user->can('location-delete');
$canView = Yii::$app->user->can('location-view');
$canCreate = Yii::$app->user->can('location-create');
$canPlan =  Yii::$app->user->can('location-plant');
$canShop =  Yii::$app->user->can('location-shop');
$canLine =  Yii::$app->user->can('location-line');
$canSector =  Yii::$app->user->can('location-sector');
?>
<div class="location-index">


    <p>
        <? if ($canPlan) { ?>
            <?= Html::a(Yii::t('app', 'btn-plant'), ['plant'], ['class' => 'btn btn-primary btn-sm btn-plant form-modal']) ?>
        <? } ?>
        <? if ($canShop) { ?>
            <?= Html::a(Yii::t('app', 'btn-shop'), ['shop'], ['class' => 'btn btn-primary btn-sm btn-shop form-modal']) ?>
        <? } ?>
        <? if ($canLine) { ?>
            <?= Html::a(Yii::t('app', 'btn-line'), ['line'], ['class' => 'btn btn-primary btn-sm form-modal']) ?>
        <? } ?>
        <? if ($canSector) { ?>
            <?= Html::a(Yii::t('app', 'btn-sector'), ['sector'], ['class' => 'btn btn-primary btn-sm form-modal']) ?>
        <? } ?>
        <? if ($canCreate) { ?>
            <?= Html::a(Yii::t('app', 'btn-create'), ['create'], ['class' => 'btn btn-success btn-sm pull-right form-modal']) ?>
        <? } ?>
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
                'headerOptions' => ['style' => 'width: auto;text-align: center;color: #3c8dbc;'],
                'contentOptions' => ['style' => 'width: auto;text-align: center;']
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete} ',
                'header' => '<i class="fa fa-fw fa-gears"></i>',
                'headerOptions' => ['style' => 'width: 70px;text-align: center;vertical-align:middle;color: #3c8dbc;'],
                'contentOptions' => ['style' => 'width: 70px;text-align: center;vertical-align:middle;'],
                'buttons' => [
                    'update' => function ($url, $model) use ($canUpdate) {
                        if (!$canUpdate) return false;
                        return Html::a(
                            '<span  class="glyphicon glyphicon-pencil"></span>',
                            $url,
                            [
                                'title' => Yii::t('app', 'Update')
                            ]
                        );
                    },
                    'delete' => function ($url, $model) use ($canDelete) {
                        if (!$canDelete) return false;
                        return Html::a(
                            '<span class="glyphicon glyphicon-trash"></span>',
                            $url,
                            [
                                'data-pjax' => 0,
                                'data-confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                                'data-method' => 'post',
                                'title' => Yii::t('app', 'Delete')
                            ]
                        );
                    },
                    'view' => function ($url, $model) use ($canView) {
                        if (!$canView) return false;
                        return Html::a(
                            '<span class="glyphicon glyphicon-eye-open"></span>',
                            $url,
                            [
                                'title' => Yii::t('app', 'View')
                            ]
                        );
                    }
                ],
                'visible' => $canDelete || $canUpdate || $canView
            ],

            [
                'attribute' => 'location_type_id',
                'value' => 'locationType.name',
                'filter' => $locationtypes
            ],
            'code',
            'name',
            'alias',
            //'is_main',
            //'area',
            //'conveyor_type_id',
            //'parent_id',
            //'address',

        ],
    ]); ?>
    <?php Pjax::end(); ?>

</div>