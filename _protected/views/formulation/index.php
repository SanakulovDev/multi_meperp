<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\FormulationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Formulations');
$this->params['breadcrumbs'][] = $this->title;
$canView = Yii::$app->user->can('formulation-view');
$canUpdate = Yii::$app->user->can('formulation-update');
$canDelete = Yii::$app->user->can('formulation-delete');
?>
<div class="formulation-index">

    <p class="pull-right">
      <?php
      if (Yii::$app->user->can('formulation-create'))
        echo Html::a(Yii::t('app', 'btn-create'), ['create'],
          [
            'class' => 'btn btn-success btn-sm',
            'data-intro' => Yii::t('intro', 'add-new-record')
          ]);
      if (Yii::$app->user->can('formulation-xls'))
        echo Html::a(Yii::t('app', 'btn-download'), ['xls', 'FactorySearch' => ($_GET['FactorySearch'] ?? null)],
          [
            'class' => 'btn btn-info btn-sm',
            'data-intro' => Yii::t('intro', 'download-button')
          ]);
      ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

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
                'headerOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
                'contentOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;'],                
                'visible' => Yii::$app->user->can('pe')
            ],
            

            'id',
            'formulation_base_id',
            'amount',
            'customer_id',
            'order_no',
            //'ulock',
            //'due_at',
            //'start_at',
            //'finish_at',
            //'act_rate',
            //'grind',
            //'packages:ntext',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
