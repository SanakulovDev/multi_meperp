<?php

use app\models\AirShipment;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AirShipmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Air shipment');
$this->params['breadcrumbs'][] = $this->title;
$canUpdate = Yii::$app->user->can('air-shipment-update');
$canDelete = Yii::$app->user->can('air-shipment-delete');
$cityName = Yii::t('app','City');
$countyName = Yii::t('app','Country');

?>
<div class="air-shipment-index">
	<div class="pull-left">
		<?php if(Yii::$app->user->can('superadmin')) echo $this->render('_lock'); ?>
	</div>
	<div class="pull-right">
		
		<?php		
	  if (Yii::$app->user->can('air-shipment-create')) {
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
	  if (Yii::$app->user->can('air-shipment-xls')) {
	  	echo Html::a(
			Yii::t('app', 'btn-download'),
			['xls', 'AirShipmentSearch' => ($_GET['AirShipmentSearch'] ?? null)],
			[
				'class' => 'btn btn-info btn-sm',
				'data-intro' => Yii::t('intro', 'download-button')
			]
		);
	  }
	  ?>
	</div>

	<?php Pjax::begin(['id' => 'pjaxGrid']); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'summary' => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
		'options' => ['style' => 'overflow:auto;clear:both'],
		'emptyText' => Yii::t('app', 'No results found.'),
		'tableOptions' => [
			'class' => 'sm_filter table table-striped table-bordered table-condensed table-sm-padding_2_0',
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
			// ['class' => 'yii\grid\SerialColumn'],
			[
				'class' => 'yii\grid\ActionColumn',
				'template' => '{update} {delete} ',
				'header' => '<i class="fa fa-fw fa-gears"></i>',
				'headerOptions' => ['style' => 'min-width:30px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
				'contentOptions' => ['style' => 'min-width:30px;text-align:center;vertical-align:middle;'],
				'buttons' => [
					'update' => function ($url, $model) use ($canUpdate) {
						if (!$canUpdate || $model->status == AirShipment::STATUS_INACTIVE) {
							return false;
						}
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
					'delete' => function ($url, $model) use ($canDelete) {
						if (!$canDelete || $model->status == AirShipment::STATUS_INACTIVE) {
							return false;
						}
						return Html::a(
							'<span class="glyphicon glyphicon-trash"></span>',
							false,
							[
								'class' => 'modalButtonDelete',
								'data-href' => $url,
								'data-grid' => 'pjaxGrid',
								'title' => Yii::t('app', 'Delete')
							]
						);
					}
				],
				'visible' => $canDelete || $canUpdate
			],
			// 'id',
			[
				'attribute' => 'supplier_id',
				'content' => function ($model) {
					return $model->supplier ? $model->supplier->name : '';
				},
				'contentOptions' => function ($model) {
					return [
						'title' => $model->supplier ? $model->supplier->name : '', 
						'class' => 'td-nowrap', 
						'style' => 'max-width:250px'
					];
				}
			],
			[
				'header' => $countyName,
				'content' => function ($model) {
					return $model->supplier ? $model->supplier->countryCode->name_en : '';
				},
				'contentOptions' => function ($model) {
					return [
						'title' => $model->supplier ? $model->supplier->countryCode->name_en : '', 
						'class' => 'td-nowrap', 
						'style' => 'max-width:150px'
					];
				}
			],
			[
				'headerOptions' => ['style' => 'width: 100px;text-align: center;vertical-align:middle;'],
				'header' => $cityName,
				'content' => function ($model) {
					return $model->supplier ? $model->supplier->city : '';
				}
			],
			[
				'headerOptions' => ['style' => 'width: 95px;text-align: center;vertical-align:middle;'],
				'contentOptions' => ['style' => 'width: 95px;text-align: right;'],
				'attribute' => 'volume',
				'format' => 'integer'
			],
			[
				'headerOptions' => ['style' => 'width: 100px;text-align: center;vertical-align:middle;'],
				'contentOptions' => ['style' => 'width: 100px;text-align: right;'],
				'attribute' => 'cost',
				'format' => 'integer'
			],
			[
				'headerOptions' => ['style' => 'width: 95px;text-align: center;vertical-align:middle;'],
				'contentOptions' => ['style' => 'width: 95px;text-align: center;'],
				'attribute' => 'period',
			],
			[
				'attribute' => 'air_shipment_reason_id',
				'filter' => Html::activeDropDownList($searchModel, 'air_shipment_reason_id', $reasons, ['class' => 'form-control select2', 'prompt' => '...']),
				'value' => 'airShipmentReason.title',
				'contentOptions' => function ($model) {
					//return $model->airShipmentReason ? $model->airShipmentReason->title : '';
					return [
						'title' => $model->airShipmentReason ? $model->airShipmentReason->title : null, 
						'class' => 'td-nowrap', 
						'style' => 'max-width:150px'
					];
				}
			],
			[
				'attribute' => 'remark',
				'contentOptions' => function ($model) {
					return [
						'title' => $model->remark, 
						'class' => 'td-nowrap', 
						'style' => 'max-width:150px'
					];
				}
			],
			[
				'headerOptions' => ['style' => 'width: 120px;text-align: center;vertical-align:middle;'],
				'attribute' => 'updated_at',
				'value' => 'updatedAtFormatted'
			],
			[
				'attribute' => 'status',
				'content' => function($model){
					return $model->status === AirShipment::STATUS_ACTIVE ? '<i class="fa fa-unlock text-warning"></i>' : '<i class="fa fa-lock text-danger"></i>';
				}
			]
		],
	]); ?>

	<?php Pjax::end(); ?>

</div>