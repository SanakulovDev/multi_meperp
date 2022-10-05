<?php
	use yii\grid\GridView;
	use yii\helpers\Html;
	use yii\widgets\Pjax;

	/* @var $this yii\web\View */
	/* @var $searchModel app\models\GtdSearch */
	/* @var $dataProvider yii\data\ActiveDataProvider */
	$this->title = Yii::t('app', 'Customs declaration');
	$this->params['breadcrumbs'][] = $this->title;
    $canView = Yii::$app->user->can('gtd-view');
    $canDelete = Yii::$app->user->can('gtd-delete');
?>
<div class="gtd-index">

	<p class="pull-right">
		<?php
        if (Yii::$app->user->can('gtd-create'))
            echo Html::a(Yii::t('app', 'btn-create'), ['create'],
		           [
			           'class' => 'btn btn-success btn-sm',
			           'style' => 'margin-right: 5px',
			           'data-intro' => Yii::t('intro', 'add-new-record')
		           ]);
        if (Yii::$app->user->can('gtd-xls'))
            echo Html::a(Yii::t('app', 'btn-download'), ['xls', 'GtdSearch' => ($_GET['GtdSearch'] ?? null)],
		           [
			           'class' => 'btn btn-info btn-sm',
			           'data-intro' => Yii::t('intro', 'download-button')
		           ]);
		?>
	</p>

	<?php Pjax::begin(); ?>

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
				[
					'class' => 'yii\grid\SerialColumn',
					'header' => '№',
					'headerOptions' => ['style' => 'width: auto;text-align: center;color: #3c8dbc;'],
					'contentOptions' => ['style' => 'width: auto;text-align: center;']
				],
				[
					'class' => 'yii\grid\ActionColumn',
					'template' => '{update} {view} {delete} ',
					'header' => '<i class="fa fa-fw fa-gears"></i>',
					'headerOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
					'contentOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;'],

                  'buttons' => [
                    'view' => function ($url, $model) use ($canView) {
                      if (!$canView) return false;
                      return Html::a(
                        '<span  class="glyphicon glyphicon-eye-open"></span>',
                        $url,
                        [
                          'title' => Yii::t('app', 'View')
                        ]
                      );
                    },
                    'delete' => function ($url, $model) use ($canDelete) {
                      if (!$canDelete) return false;
                      return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                        $url,
                        [
                          'data-pjax' => 0,
                          'data-confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                          'data-method' => 'post',
                          'title' => Yii::t('app', 'Delete')
                        ]);
                    }
                  ],
                  'visible' => $canDelete || $canView,
				],
				'gtd_no',
				'gtd_dt',
				'post_no',
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

</div>
