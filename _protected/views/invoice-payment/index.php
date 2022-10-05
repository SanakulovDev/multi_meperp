<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\InvoicePaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Payment — invoice');
$this->params['breadcrumbs'][] = $this->title;

$canUpdate = Yii::$app->user->can('invoice-payment-update');
$canDelete = Yii::$app->user->can('invoice-payment-delete');
$canCreate = Yii::$app->user->can('invoice-payment-create');
$canDownload = Yii::$app->user->can('invoice-payment-xls');
?>
<div class="invoice-payment-index">

    <p class="pull-right">
        <?php
        if ($canCreate) {
        echo Html::a(
            Yii::t('app', 'btn-create'),
            ['create'],
            [
                'class' => 'btn btn-success btn-sm form-modal',
                'style' => 'margin-right: 5px',
                'data-intro' => Yii::t('intro', 'add-new-record')
            ]
        );
        }
        if ($canDownload) {
			echo Html::a(
				Yii::t('app', 'btn-download'),
				['xls', 'InvoicePaymentSearch' => ($_GET['InvoicePaymentSearch'] ?? null)],
				[
					'class' => 'btn btn-info btn-sm',
					'data-intro' => Yii::t('intro', 'download-button')
				]
			);
		}
	  ?>
    </p>
    <?php Pjax::begin(['id' => 'pjaxGrid']); ?>
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
    		['class' => 'yii\grid\SerialColumn'],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete} ',
                'header' => '<i class="fa fa-fw fa-gears"></i>',
                'headerOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
                'contentOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;'],
                'buttons' => [
                    'update' => function ($url, $model) use ($canUpdate) {
                        if (!$canUpdate) {
                            return false;
                        }
                        return Html::a(
                            '<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>',
                            false,
                            [
                                'class' => 'modalButtonUpdate',
                                'value' => $url,
                                'title' => Yii::t('app', 'Edit')
                            ]
                        );
                    },
                    'delete' => function ($url, $model) use ($canDelete) {
                        if (!$canDelete) {
                            return false;
                        }
                        return Html::a(
                            '<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
                            false,
                            [
                                'class' => 'modalButtonDelete',
                                'data-href' => $url,
                                'data-grid' => 'pjaxGrid',
                                'title' => Yii::t('app', 'Delete')
                            ]
                        );
                    },
                ],
                'visible' => $canUpdate || $canDelete
            ],
            'id',
            [
                'attribute' => 'payment_control_id',
                'content' => function($model) {
                    return Html::a(
                        $model->paymentControl->typeName. ': '. $model->paymentControl->no.' ('.$model->paymentControl->date.')',
                        Url::to(['payment-control/index', 'PaymentControlSearch[id]'=>$model->payment_control_id])
                    )  ;
                }
            ],
            [
                'attribute' => 'invoice_id',
                'content' => function($model) {
                    return Html::a(
                        $model->invoice->invoice_no,
                        Url::to(['invoice/index', 'InvoiceSearch[id]'=>$model->invoice_id])
                    )  ;
                }
            ],
            'amount:decimal',
            [
                'attribute' => 'updated_by',
                'value' => 'updatedBy.fullname'
            ],
            [
                'attribute' => 'updated_at',
                'value' => 'updatedAtFormatted'
            ]
    	],
    ]); ?>

    <?php Pjax::end(); ?>

</div>