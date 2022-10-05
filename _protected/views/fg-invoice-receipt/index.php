<?php

use app\components\Helpers;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PaymentControlSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Fg invoice receipt');
$this->params['breadcrumbs'][] = $this->title;
$canUpdate = Yii::$app->user->can('fg-invoice-receipt-update');
$canDelete = Yii::$app->user->can('fg-invoice-receipt-delete');
$canCreate = Yii::$app->user->can('fg-invoice-receipt-create');
$canDownload = Yii::$app->user->can('fg-invoice-receipt-xls');
?>
<div class="payment-control-index">
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
				['xls', 'WaybillReceiptsSearch' => ($_GET['WaybillReceiptsSearch'] ?? null)],
				[
					'class' => 'btn btn-info btn-sm',
					'data-intro' => Yii::t('intro', 'download-button')
				]
			);
		}
		?>
	</p>
	<?php Pjax::begin(['id' => 'pjaxGrid']); ?>
	<?=
		GridView::widget(
			[
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
          [
						'attribute' => 'customer',
            'value' => function ($model, $column) {
              return $model->fgInvoice->customer ? $model->fgInvoice->customer->name : '';
            }
					],
          [
            'attribute' => 'contract',
            'value' => function ($model, $column) {
              return $model->receptControl->salesContract ? $model->receptControl->salesContract->contract_no : '';
            }
          ],
          [
            'attribute' => 'paymentNo',
            'value' => function ($model, $column) {
              return $model->receptControl->no;
            }
          ],
          [
            'attribute' => 'amount',
            'contentOptions' => ['style' => 'vertical-align:middle;text-align:right'],
            'value' => function ($model) {
              return Helpers::numberFormatRemoveZero($model->amount);
            },
          ],
          [
            'attribute' => 'paymentDate',
            'value' => function ($model, $column) {
              return $model->receptControl->date;
            }
          ],
          [
            'attribute' => 'fg_invoice_id',
            'value' => function ($model, $column) {
              return $model->fgInvoice->invoice_no;
            }
          ],
					[
						'header' => Yii::t('app', 'Currency'),
						'value' => function ($model, $column) {
              return ($model->receptControl->salesContract && $model->receptControl->salesContract->currency) ? $model->receptControl->salesContract->currency->code : '';
						},
					],

				],
			]
		); ?>
	<?php Pjax::end(); ?>
</div>