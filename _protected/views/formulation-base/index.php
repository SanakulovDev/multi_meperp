<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\FormulationBaseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Formulation Bases');
$this->params['breadcrumbs'][] = $this->title;
$canUpdate = Yii::$app->user->can('formulation-base-update');
$canDelete = Yii::$app->user->can('formulation-base-delete');
$canView = Yii::$app->user->can('formulation-base-view');
?>
<div class="formulation-base-index">



    <p class="pull-right">
        <? if (Yii::$app->user->can('formulation-base-create')) { ?>
            <?= Html::a(Yii::t('app', 'btn-create'), ['create'], ['class' => 'btn btn-success']) ?>
        <? } ?>
    </p>

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
                'template' => '{view}{update}{delete} ',
                'header' => '<i class="fa fa-fw fa-gears"></i>',
                'headerOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
                'contentOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;'],
                'buttons' => [
                    'view' => function ($url, $model) use ($canView) {
                        if (!$canView) return false;
                        $url = Url::toRoute(['formulation-base/view', 'id' => $model->id]);
                        return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>', $url, [
                            'title' => Yii::t('app', 'View')
                        ]) . '&nbsp;';
                    },
                    'update' => function ($url, $model) use ($canUpdate) {
                        if (!$canUpdate) return false;
                        $url = Url::toRoute(['formulation-base/update', 'id' => $model->id]);
                        return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', $url, [
                            'title' => Yii::t('app', 'Edit')
                        ]) . '&nbsp;';
                    },
                    'delete' => function ($url, $model) use ($canDelete) {
                        if (!$canDelete) return false;
                        $url = Url::toRoute(['formulation-base/delete', 'id' => $model->id]);
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

            'part_id',
            'pack',
            'version',
            'status',
        ],
    ]); ?>


</div>