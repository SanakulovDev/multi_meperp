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

$bgClass1 = '';
$bgClass2 = '';
if($status == 2){
    $bgClass1 = 'btn-light';
    $bgClass2 = 'btn-success';
}
else{
    $bgClass1 = 'btn-success';
    $bgClass2 = 'btn-light';
}
?>
<?php ob_start();?>
.month-day-btn{
    border: 1.5px solid black;
    width: 120px;
}
.sort-month-btn{
    border-right: none;
}
.modal-dialog{
    width: 1200px;
}
<?php $this->registerCss(ob_get_clean());?>
<div class="product-group-index">
    <div class="btn-group">
        <!-- month btn -->
        <?=Html::a(Yii::t('app', 'Month'),
            ['index'], [
                'class' => 'btn '.$bgClass1.' btn-lg month-day-btn sort-btn',
                'data-param'=> 'SalesPlanSearch%5Bstatus%5D=1',
                'data-intro' => Yii::t('intro', 'month-btn'),
            ]
        )?>
        <!-- day btn -->
        <?=Html::a(Yii::t('app', 'Days'),
            ['index-day'], [
                'class' => 'btn '.$bgClass2.' btn-lg  month-day-btn sort-btn',
                'data-param'=>'SalesPlanSearch%5Bstatus%5D=2',
                'data-intro' => Yii::t('intro', 'day-btn'),
            ]
        )?>
    </div>
    <p class="pull-right">
		<? if ($canCreate) { ?>
            <?php if($status == 2):?>
                <?=Html::a(Yii::t('app', 'btn-create'),
                    ['create-day'], [
                        'class' => 'btn btn-success btn-sm form-modal',
                        'data-intro' => Yii::t('intro', 'add-new-record')
                    ]
                )?>
            <?php elseif($status == 1):?>
                <?=Html::a(Yii::t('app', 'btn-create'),
                    ['create'], [
                        'class' => 'btn btn-success btn-sm form-modal',
                        'data-intro' => Yii::t('intro', 'add-new-record')
                    ]
                )?>
            <?php endif;?>
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

<?php ob_start();?>
$(function(){

    $('body').on('click', '.sort-btn', function(e){
        e.preventDefault();
        let param = $(this).data('param');
        console.log(param);
        let url = '<?=Url::toRoute(['sales-plan/index?'])?>'+param;
        // redirect url
        window.location.href = url;
    })
})

<?php $this->registerJs(ob_get_clean());?>
