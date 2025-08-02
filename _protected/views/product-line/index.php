<?php
	use yii\grid\GridView;
	use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

	/* @var $this yii\web\View */
	/* @var $searchModel app\models\ProductLineSearch */
	/* @var $dataProvider yii\data\ActiveDataProvider */
	$this->title = Yii::t('app', 'The production lines');
	$this->params['breadcrumbs'][] = $this->title;
	$canUpdate = Yii::$app->user->can('product-line-update');
	$canDelete = Yii::$app->user->can('product-line-delete');
	$canCreate = Yii::$app->user->can('product-line-create');
?>
<div class="product-line-index">
	<p class="pull-right">
		<? if ($canCreate) { ?>
		<?=
			Html::a(Yii::t('app', 'btn-create'), ['create'], [
				                                   'class' => 'btn btn-success btn-sm',
				                                   'data-intro' => Yii::t('intro', 'add-new-record')
			                                   ]
			)
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
					[
						'class' => 'yii\grid\SerialColumn',
						'headerOptions' => ['style' => 'width:50px;text-align:center;vertical-align:middle;'],
						'contentOptions' => ['style' => 'width:50px;text-align:center;vertical-align:middle;'],
					],
					[
						'class' => 'yii\grid\ActionColumn',
						'template' => '{update} {delete} ',
						'header' => '<i class="fa fa-fw fa-gears"></i>',
						'headerOptions' => ['style' => 'width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
						'contentOptions' => ['style' => 'width:50px;text-align:center;vertical-align:middle;'],
						'buttons' => [
							'update' => function ($url, $model) use($canUpdate) {
								if(!$canUpdate) return false;
								$url = Url::toRoute(['product-line/update', 'id' => $model->id]);
								return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', $url, [
									'title' => Yii::t('app', 'Edit')
								]) . '&nbsp;';
							},
							'delete' => function ($url, $model) use($canDelete) {
								if(!$canDelete) return false;
								$url = Url::toRoute(['product-line/delete', 'id' => $model->id]);
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
					'linename:text',
					'description',
                    [
                      'attribute' => 'is_zone',
                      'filter' => $searchModel->statusList(),
                      'contentOptions' => ['style' => 'text-align: center;'],
                      'content' => function($model, $column){
                        $zone_value = $model->is_zone;
                        switch($zone_value){
                          case 1:
                            $class = 'success';
                            $zone_name = "✔";
                            $zone_title = Yii::t('app', 'Active');
                            break;
                          case 0:
                            $class = 'danger';
                            $zone_name = "✖";
                            $zone_title = Yii::t('app', 'Inactive');
                            break;
                        }
                        $html = Html::tag('span', Html::encode($zone_name), ['title' => $zone_title, 'class' => 'label label-'.$class]);
                        return $zone_value === null ? '-' : $html;
                      },
                    ],
				],
			]);
	?>
	<?php Pjax::end(); ?>
</div>
