<?php
	use yii\grid\GridView;
	use yii\helpers\Html;
	use yii\helpers\Url;

	/* @var $this yii\web\View */
	/* @var $searchModel app\models\FactorySearch */
	/* @var $dataProvider yii\data\ActiveDataProvider */
	$this->title = Yii::t('app', 'Factories');
	$this->params['breadcrumbs'][] = $this->title;
	$canUpdate = Yii::$app->user->can('factory-update');
	$canDelete = Yii::$app->user->can('factory-delete');
	$canView = Yii::$app->user->can('factory-view');
?>
<div class="factory-index">

	<p class="pull-right">
		<? if(Yii::$app->user->can('factory-create')){ ?>
			<?=Html::a(
				Yii::t('app', 'btn-create'),
				['create'],
				[
					'class' => 'btn btn-success btn-sm',
					'data-intro' => Yii::t('intro', 'add-new-record')
				]
			)?>
		<? } ?>
		<? if(Yii::$app->user->can('factory-xls')){ ?>
			<?=Html::a(
				Yii::t('app', 'btn-download'),
				['xls', 'FactorySearch' => ($_GET['FactorySearch'] ?? null)],
				[
					'class' => 'btn btn-info btn-sm',
					'data-intro' => Yii::t('intro', 'download-button')
				]
			)?>
		<? } ?>
	</p>

	<?=GridView::widget(
		[
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
				['class' => 'yii\grid\SerialColumn'],
				[
					'class' => 'yii\grid\ActionColumn',
					'template' => '{view}{update}{delete}',
					'header' => '<i class="fa fa-fw fa-gears"></i>',
					'headerOptions' => ['style' => 'min-width:80px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
					'contentOptions' => ['style' => 'min-width: 80px;text-align: center;vertical-align:middle;'],
					'buttons' => [
						'view' => function($url, $model) use ($canView){
							if(!$canView) return false;
							$url = Url::toRoute(['factory/view', 'id' => $model->id]);
							return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>', $url, [
									'title' => Yii::t('app', 'View')
								]).'&nbsp;';
						},
						'update' => function($url, $model) use ($canUpdate){
							if(!$canUpdate) return false;
							$url = Url::toRoute(['factory/update', 'id' => $model->id]);
							return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', $url, [
									'title' => Yii::t('app', 'Edit')
								]).'&nbsp;';
						},
						'delete' => function($url, $model) use ($canDelete){
							if(!$canDelete) return false;
							$url = Url::toRoute(['factory/delete', 'id' => $model->id]);
							return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>', $url, [
									'title' => Yii::t('app', 'Delete'),
									'data' => [
										'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
										'method' => 'post',
									],
								]).'&nbsp;';
						},
					],
					'visible' => $canUpdate || $canDelete || $canView
				],
				'name',
				'head',
				'chief_accountant',
				'alias',
				[
					'label' => Yii::t('app', 'Is main'),
					'filter' => [
						'1' => Yii::t('app', 'Yes'),
						'0' => Yii::t('app', 'No'),
					],
					'attribute' => 'is_main',
					'contentOptions' => ['style' => 'text-align: center;'],
					'content' => function($model, $column){
						$isMain_value = $model->is_main;
						switch($isMain_value){
							case 1:
								$class = 'label label-success';
								$isMain_name = "✔";
								$isMain_title = Yii::t('app', 'Yes');
								break;
							case 0:
								$class = 'default';
								$isMain_name = "-";
								$isMain_title = Yii::t('app', 'No');
								break;
						}
						$html = Html::tag('span', Html::encode($isMain_name), ['class' => $class]);
						return $html;// $isMain_value === null ? $column->grid->emptyCell : $html;
					},
				],
				'address',
				'tin',
				'vat',
				'duns',
				[
					'attribute' => 'fg_warehouse_id',
					'content' => function($model, $column){
						return $model->fgWarehouse->name;
					}
				],
				'remark',
				[
					'label' => Yii::t('app', 'Status'),
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
						$html = Html::tag('span', Html::encode($sts_name), ['class' => 'label label-'.$class]);
						return $sts_value === null ? $column->grid->emptyCell : $html;
					},
				],
			],
		]
	);?>


</div>