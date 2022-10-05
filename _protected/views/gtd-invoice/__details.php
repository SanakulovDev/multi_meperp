<?php
	use yii\grid\GridView;
	use yii\helpers\Html;
	use yii\helpers\Url;
	use yii\widgets\Pjax;

?>
<?php Pjax::begin(['id' => 'pjaxGrid']); ?>
<?=
	GridView::widget(
		[
			'dataProvider' => $dataProvider,
			'filterModel' => $searchModel,
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
			'summary' => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
			'options' => ['style' => 'overflow-x:scroll;clear:both'],
			'columns' => [
				['class' => 'yii\grid\SerialColumn'],
				[
					'class' => 'yii\grid\ActionColumn',
					'template' => '{update}&nbsp;{delete}',
					'header' => '<i class="fa fa-fw fa-gears"></i>',
					'headerOptions' => ['style' => 'min-width:100px; width:auto; text-align: center;vertical-align:middle;color: #3c8dbc;'],
					'contentOptions' => ['style' => 'min-width:100px; width:auto; text-align: center;vertical-align:middle;'],
					'buttons' => [
						'update' => function($url, $model){
							$url = Url::toRoute(['gtd-invoice/update', 'id' => $model->id]);
							return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', $url, [
								'title' => Yii::t('app', 'Edit')
							]);
						},
						'delete' => function($url, $model){
							$url = Url::toRoute(['gtd-invoice/delete', 'id' => $model->id]);
							return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>', $url, [
								'title' => Yii::t('app', 'Delete'),
								'data' => [
									'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
									'method' => 'post',
								],
							]);
						},
					]
				],
				[
					'attribute' => 'invoice_id',
					'content' => function($model){
						return $model->invoice->invoice_no;
					},
					'contentOptions' => ['class' => 'td-nowrap']
				],
				'amount',
				[
					'attribute' => 'created_by',
					'value' => function($data){
						$created_val = (!empty($data->createdBy)) ? $data->createdBy->username : '-';
						return $created_val;
					},
					'contentOptions' => function($model, $key, $index, $column){
						$content_with_title = (!empty($model->createdBy)) ? ['style' => 'text-align: center;', 'title' => date('d.m.Y (H:i:s)', $model->created_at)] : ['style' => 'text-align: center;'];
						return $content_with_title;
					}
				],
				[
					'attribute' => 'updated_by',
					'value' => function($data){
						$updated_val = (!empty($data->updatedBy)) ? $data->updatedBy->username : '-';
						return $updated_val;
					},
					'contentOptions' => function($model, $key, $index, $column){
						$content_with_title = (!empty($model->updatedBy)) ? ['style' => 'text-align: center;', 'title' => date('d.m.Y (H:i:s)', $model->updated_at)] : ['style' => 'text-align: center;'];
						return $content_with_title;
					}
				]
			],
		]);?>

<?php Pjax::end(); ?>
