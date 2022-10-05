<?php
	use app\models\Supplier;
	use app\models\WarehouseReportGroup;
	use yii\grid\GridView;
	use yii\helpers\ArrayHelper;
	use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

	/* @var $this yii\web\View */
	/* @var $searchModel app\models\WarehouseSearch */
	/* @var $dataProvider yii\data\ActiveDataProvider */
	$this->title = Yii::t('app', 'Warehouses');
	$this->params['breadcrumbs'][] = $this->title;
	$canUpdate = Yii::$app->user->can('warehouse-update');
	$canDelete = Yii::$app->user->can('warehouse-delete');
	$canCreate =  Yii::$app->user->can('warehouse-create');
	$canXls = Yii::$app->user->can('warehouse-xls');
?>
<div class="warehouse-index">

		<p class="pull-right">
		<? if ($canCreate) { ?>
			<?=
				Html::a(Yii::t('app', 'btn-create'), ['create'], [
					'class' => 'btn btn-success btn-sm',
					'data-intro' => Yii::t('intro', 'add-new-record')
				])
			?>
			<? } ?>
		<? if ($canXls) { ?>
			<?=
				Html::a(Yii::t('app', 'btn-download'), ['xls', 'WarehouseSearch' => ($_GET['WarehouseSearch'] ?? null)], [
					'class' => 'btn btn-info btn-sm',
					'data-intro' => Yii::t('intro', 'download-button')
				])
			?>
			<? } ?>
		</p>

	<?php Pjax::begin(); ?>
	<?=
		GridView::widget(
			[
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
					['class' => 'yii\grid\SerialColumn'],
					[
						'class' => 'yii\grid\ActionColumn',
						'template' => '{update} {delete} ',
						'header' => '<i class="fa fa-fw fa-gears"></i>',
						'headerOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
						'contentOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;'],
						'buttons' => [
							'update' => function ($url, $model) use($canUpdate) {
								if(!$canUpdate) return false;
								$url = Url::toRoute(['warehouse/update', 'id' => $model->id]);
								return Html::a('<span class="glyphicon  glyphicon-pencil" aria-hidden="true"></span>', $url, [
									'title' => Yii::t('app', 'Edit')
								]) . '&nbsp;';
							},
							'delete' => function ($url, $model) use($canDelete) {
								if(!$canDelete) return false;
								$url = Url::toRoute(['warehouse/delete', 'id' => $model->id]);
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
					'name',
					'description',
					[
						'filter' => $searchModel->statusList,
						'attribute' => 'status',
						'contentOptions' => ['style' => 'text-align: center;'],
						'content' => function($model, $column){
							$sts_value = $model->status;
							switch($sts_value){
								case 1:
									$class = 'success';
									$sts_name = "✔";
									$sts_title = Yii::t('app', 'Active');
									break;
								case 0:
									$class = 'danger';
									$sts_name = "✖";
									$sts_title = Yii::t('app', 'Inactive');
									break;
							}
							$html = Html::tag('span', Html::encode($sts_name), ['title' => $sts_title, 'class' => 'label label-'.$class]);
							return $sts_value === null ? $column->grid->emptyCell : $html;
						},
					],
					[
						'attribute' => 'warehouse_type',
						'filter' => $searchModel->typeListNames,
						'value' => function($model){
							return $model->typeName;
						},
					],
					[
						'attribute' => 'supplier_id',
						'headerOptions' => ['style' => 'width:150px;text-align:center;vertical-align:middle;'],
						'contentOptions' => ['style' => 'width:150px;vertical-align:middle;'],
						'content' => function($model){
							return $model->supplier->name ?? null;
						},
						'filter' => ArrayHelper::map(Supplier::find()->all(), 'id', 'name')
					],
					[
						'attribute' => 'warehouse_report_group_id',
						'content' => function($model){
							return $model->warehouseReportGroup->title ?? null;
						},
						'filter' => ArrayHelper::map(WarehouseReportGroup::find()->all(), 'id', 'title')
					],
					//				                 [
					//					                 'attribute' => 'created_at',
					//					                 'value' => function($data){
					//						                 return date('d.m.Y (H:i:s)', $data->created_at);
					//					                 },
					//					                 'contentOptions' => function($model, $key, $index, $column){
					//						                 return ['title' => (!empty($model->createdBy)) ? $model->createdBy->username : '-'];
					//					                 }
					//				                 ],
					//				                 [
					//					                 'attribute' => 'updated_at',
					//					                 'value' => function($data){
					//						                 $update_val = date('d.m.Y (H:i:s)', $data->updated_at);
					//						                 return $update_val;
					//					                 },
					//					                 'contentOptions' => function($model, $key, $index, $column){
					//						                 return ['title' => (!empty($model->updatedBy)) ? $model->updatedBy->username : '-'];
					//					                 }
					//				                 ],
				],
			]);
	?>
	<?php Pjax::end(); ?>
</div>
