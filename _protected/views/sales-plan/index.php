<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Customer;
use app\models\Part;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Sales plan');
$this->params['breadcrumbs'][] = $this->title;
$canUpdate = Yii::$app->user->can('sales-plan-update');
$canDelete = Yii::$app->user->can('sales-plan-delete');
$canCreate = Yii::$app->user->can('sales-plan-create');
?>
<div class="product-group-index">

    <p class="pull-right">
		<? if ($canCreate) { ?>
        <?=Html::a(Yii::t('app', 'btn-create'),
            ['create'], [
                'class' => 'btn btn-success btn-sm form-modal',
                'data-intro' => Yii::t('intro', 'add-new-record')
            ]
        )?>
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
                'template' => '{update} {delete} ',
                'header' => '<i class="fa fa-fw fa-gears"></i>',
                'headerOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
                'contentOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;'],
                'buttons' => [
                    'update' => function($url, $model) use($canUpdate) {
                        if(!$canUpdate) return false;
                        $url = Url::toRoute(['sales-plan/update', 'id' => $model->id]);
                        return Html::a(
                            '<span  class="glyphicon glyphicon-pencil"></span>',
                            false,
                            [
                                'class' => 'modalButtonUpdate',
                                'value' => $url,
                                'title' => Yii::t('app', 'Update')
                            ]
                        );
                    },
                    'delete' => function($url, $model) use($canDelete) {
                        if(!$canDelete) return false;
                        $url = Url::toRoute(['sales-plan/delete', 'id' => $model->id]);
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                            false,
                            [
                                'class' => 'modalButtonDelete',
                                'data-href' => $url,
                                'data-grid' => 'pjaxGrid',
                                'title' => Yii::t('app', 'Delete')
                            ]);
                    }
                ],
                'visible' => $canUpdate || $canDelete
            ],
            [
              'attribute' => 'customer_id',
              'headerOptions' => ['style' => 'width: 200px;text-align: left;vertical-align:middle;'],
              'contentOptions' => ['class' => 'td-nowrap'],
              'filter' => Html::activeDropDownList($searchModel, 'customer_id', ArrayHelper::map(Customer::find()->all(), 'id', 'name'), ['class' => 'form-control select2', 'prompt' => '...']),
              'content' => function ($model) {
                return $model->customer->name;
              },
            ],
            [
              'attribute' => 'part_id',
              'headerOptions' => ['style' => 'width: 200px;text-align: left;vertical-align:middle;'],
              'contentOptions' => ['class' => 'td-nowrap'],
              'filter' => Html::activeDropDownList($searchModel, 'part_id', ArrayHelper::map(Part::find()->all(), 'id', 'partinfo'), ['class' => 'form-control select2', 'prompt' => '...']),
              'content' => function ($model) {
                return $model->part->partinfo;
              },
            ],
            'target_qty',
            'target_date',
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
