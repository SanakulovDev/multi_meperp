<?php
	use yii\grid\GridView;
	use yii\helpers\Html;
	use yii\helpers\Url;

	/* @var $this yii\web\View */
	/* @var $searchModel app\models\WarehouseReportGroupSearch */
	/* @var $dataProvider yii\data\ActiveDataProvider */
	$this->title = Yii::t('app', 'Warehouse report groups');
	$this->params['breadcrumbs'][] = $this->title;
	$canUpdate = Yii::$app->user->can('warehouse-report-group-update');
	$canDelete = Yii::$app->user->can('warehouse-report-group-delete');
	$canCreate =  Yii::$app->user->can('warehouse-report-group-create');
?>
<div class="warehouse-report-group-index">
	<p class="pull-right">
		<? if ($canCreate) { ?>
		<?=Html::a(Yii::t('app', 'btn-create'), ['create'],
		           [
			           'class' => 'btn btn-success btn-sm',
			           'data-intro' => Yii::t('intro', 'add-new-record')
		           ])
		?>
        <? } ?>
	</p>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

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
					'header' => '№',
					'headerOptions' => ['style' => 'width:70px;text-align: center;color: #3c8dbc;'],
					'contentOptions' => ['style' => 'width:70px;text-align: center;']
				],
				[
					'class' => 'yii\grid\ActionColumn',
					'header' => '<i class="fa fa-fw fa-gears"></i>',
					'headerOptions' => ['style' => 'width:70px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
					'contentOptions' => ['style' => 'width:70px;text-align:center;vertical-align:middle;'],
					'template' => ' {update} {delete}',
					'buttons' => [
						'update' => function ($url, $model) use($canUpdate) {
							if(!$canUpdate) return false;
							$url = Url::toRoute(['warehouse-report-group/update', 'id' => $model->id]);
							return Html::a('<span class="glyphicon  glyphicon-pencil" aria-hidden="true"></span>', $url, [
								'title' => Yii::t('app', 'Edit')
							]) . '&nbsp;';
						},
						'delete' => function ($url, $model) use($canDelete) {
							if(!$canDelete) return false;
							$url = Url::toRoute(['warehouse-report-group/delete', 'id' => $model->id]);
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
				'title',
				'description',
				[
					'attribute' => 'sort_order',
					'headerOptions' => ['style' => 'width:100px;text-align:center;vertical-align:middle;'],
					'contentOptions' => ['style' => 'width:100px;text-align:center;vertical-align:middle;'],
				]
				
			],
		]);?>


</div>
