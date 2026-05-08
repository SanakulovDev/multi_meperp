<?php
use app\components\Helpers;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\FgInvoicePaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $customers array  id => name */
/* @var $contracts array  id => contract_no */
/* @var $currencies array id => code */
/* @var $waybills  array  fg_invoice_id => invoice_no */

$select2prompt = Yii::t('app', 'All');

$this->title = Yii::t('app', 'FG Invoice Payments');
$this->params['breadcrumbs'][] = $this->title;

$canCreate = Yii::$app->user->can('fg-invoice-payment-create');
$canUpdate = Yii::$app->user->can('fg-invoice-payment-update');
$canDelete = Yii::$app->user->can('fg-invoice-payment-delete');
$canXls    = Yii::$app->user->can('fg-invoice-payment-xls');
?>
<div class="fg-invoice-payment-index">
    <p class="pull-right">
        <?php if ($canCreate): ?>
            <?= Html::a(Yii::t('app', 'btn-create'), ['create'], [
                'class' => 'btn btn-success btn-sm form-modal',
                'style' => 'margin-right:5px',
            ]) ?>
        <?php endif; ?>
        <?php if ($canXls): ?>
            <?= Html::a(Yii::t('app', 'btn-download'),
                ['xls', 'FgInvoicePaymentSearch' => ($_GET['FgInvoicePaymentSearch'] ?? null)],
                ['class' => 'btn btn-info btn-sm']
            ) ?>
        <?php endif; ?>
    </p>

    <?php Pjax::begin(['id' => 'pjaxGrid']); ?>
    <?= GridView::widget([
        'dataProvider'    => $dataProvider,
        'filterModel'     => $searchModel,
        'summary'         => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
        'rowOptions'      => ['style' => 'white-space:nowrap;vertical-align:middle;'],
        'options'         => ['style' => 'overflow:auto;clear:both'],
        'emptyText'       => Yii::t('app', 'No results found.'),
        'tableOptions'    => ['class' => 'sm_filter table table-striped table-bordered table-condensed table-sm-padding_2_0'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'class'    => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'header'   => '<i class="fa fa-fw fa-gears"></i>',
                'headerOptions'  => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
                'contentOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;'],
                'buttons' => [
                    'update' => function ($url, $model) use ($canUpdate) {
                        if (!$canUpdate) return false;
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', false, [
                            'class' => 'modalButtonUpdate',
                            'value' => $url,
                            'title' => Yii::t('app', 'Edit'),
                        ]);
                    },
                    'delete' => function ($url, $model) use ($canDelete) {
                        if (!$canDelete) return false;
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', false, [
                            'class'     => 'modalButtonDelete',
                            'data-href' => $url,
                            'data-grid' => 'pjaxGrid',
                            'title'     => Yii::t('app', 'Delete'),
                        ]);
                    },
                ],
                'visible' => $canUpdate || $canDelete,
            ],
            [
                'attribute' => 'customer_id',
                'label'     => Yii::t('app', 'Customer'),
                'filter'    => Html::activeDropDownList($searchModel, 'customer_id', $customers, ['class' => 'form-control select2', 'prompt' => $select2prompt]),
                'value'     => function ($model) { return $model->salesContract && $model->salesContract->customer ? $model->salesContract->customer->name : ''; },
            ],
            [
                'attribute' => 'fg_invoice_id',
                'label'     => Yii::t('app', 'Invoice no'),
                'filter'    => Html::activeDropDownList($searchModel, 'fg_invoice_id', $waybills, ['class' => 'form-control select2', 'prompt' => $select2prompt]),
                'value'     => function ($model) { return $model->invoiceNo; },
            ],
            [
                'attribute' => 'currency_id',
                'label'     => Yii::t('app', 'Currency'),
                'filter'    => Html::activeDropDownList($searchModel, 'currency_id', $currencies, ['class' => 'form-control select2', 'prompt' => $select2prompt]),
                'value'     => function ($model) { return $model->currency ? $model->currency->code : ''; },
            ],
            
            [
                'attribute'      => 'amount',
                'contentOptions' => ['style' => 'text-align:right;vertical-align:middle;'],
                'value'          => function ($model) { return Helpers::numberFormatRemoveZero($model->amount); },
            ],
            'no',
            'date',
            [
                'attribute' => 'created_by',
                'label'     => Yii::t('app', 'Created by'),
                'value'     => function ($model) { return $model->createdBy->fullname ?? ''; },
            ],
            [
                'attribute' => 'created_at',
                'label'     => Yii::t('app', 'Created at'),
                'value'     => function ($model) { return $model->createdAtFormatted; },
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
