<?php
	use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
	/* @var $searchModel app\models\InvoiceSearch */
	/* @var $dataProvider yii\data\ActiveDataProvider */
	$this->title = Yii::t('app', 'Invoice');
	$this->params['breadcrumbs'][] = $this->title;
	$canUpdate = Yii::$app->user->can('invoice-update');
	$canDownload = Yii::$app->user->can('invoice-xls');
?>
<div class="invoice-index">
	<p class="pull-right">
		<?php
		if ($canDownload) {
			echo Html::a(
				Yii::t('app', 'btn-download'),
				['xls', 'InvoiceSearch' => ($_GET['InvoiceSearch'] ?? null)],
				[
					'class' => 'btn btn-info btn-sm',
					'data-intro' => Yii::t('intro', 'download-button')
				]
			);
		}
	  ?>
	</p>
	<?php Pjax::begin(['id' => 'pjaxGrid']); ?>

	<?=GridView::widget(
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
					[
						'class' => 'yii\grid\SerialColumn',
						'headerOptions' => ['style' => 'width:50px;text-align:right;vertical-align:middle;color:#3c8dbc;'],
						'contentOptions' => ['style' => 'width:50px;text-align:right;vertical-align:middle;'],
					],
					[
						'class' => 'yii\grid\ActionColumn',
						'template' => '{update} ',
						'header' => '<i class="fa fa-fw fa-gears"></i>',
						'headerOptions' => ['style' => 'width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
						'contentOptions' => ['style' => 'width:50px;text-align:center;vertical-align:middle;'],
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
							// 'delete' => function ($url, $model) use ($canDelete) {
							// 	if (!$canDelete) {
							// 		return false;
							// 	}
							// 	return Html::a(
							// 		'<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
							// 		false,
							// 		[
							// 			'class' => 'modalButtonDelete',
							// 			'data-href' => $url,
							// 			'data-grid' => 'pjaxGrid',
							// 			'title' => Yii::t('app', 'Delete')
							// 		]
							// 	);
							// },
						],
						'visible' => $canUpdate
					],
					'invoice_no',
					[
						'attribute' => 'invoice_date',
						'filter' => Html::activeInput('date', $searchModel,'invoice_date', ['class' => 'form-control']),
						'headerOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
						'contentOptions' => ['style' => 'width: 150px;text-align: center;vertical-align:middle;'],
					],
					[
						'headerOptions' => ['style' => 'width: 100px;text-align: right;vertical-align:middle;'],
						'contentOptions' => ['style' => 'width: 100px;text-align: right;vertical-align:middle;'],
						'attribute' => 'invoice_amount',
						'value' => 'invoice_amount',
						'format' => 'decimal'
					],
					[
						'attribute' => 'supplier_id',
						'value' => 'supplier.name',
						'contentOptions' => function ($model, $column) {
							return ['title' => $model->supplier->name ?? null, 'class' => 'td-nowrap', 'style' => 'max-width:100px'];
						},
						// 'filter' => $suppliers
					],
					[
						'headerOptions' => ['style' => 'width: 50px;text-align: center;vertical-align:middle;'],
						'contentOptions' => ['style' => 'width: 50px;text-align: center;vertical-align:middle;'],
	  				'attribute' => 'currency_id',
	  				'value' => 'currency.code',
	  			],
	  			//			'port_of_loading',
	  			//			'package_qty',
	  			//			'cbm',
	  			//			'n_weight',
	  			//			'g_weight',
	  			//			'total_amount',
	  			// [
	  			// 	'attribute' => 'created_at',
	  			// 	'value' => function($data){
	  			// 		return date('d.m.Y (H:i:s)', $data->created_at);
	  			// 	},
	  			// 	'contentOptions' => function($model, $key, $index, $column){
	  			// 		return ['title' => (!empty($model->createdBy)) ? $model->createdBy->username : '-'];
	  			// 	}
	  			// ],
	  			// [
	  			// 	'attribute' => 'updated_at',
	  			// 	'value' => function($data){
	  			// 		$update_val = date('d.m.Y (H:i:s)', $data->updated_at);
	  			// 		return $update_val;
	  			// 	},
	  			// 	'contentOptions' => function($model, $key, $index, $column){
	  			// 		return ['title' => (!empty($model->updatedBy)) ? $model->updatedBy->username : '-'];
	  			// 	}
	  			// ],
	  		],
	  	]
	  );?>
	<?php Pjax::end(); ?>
</div>