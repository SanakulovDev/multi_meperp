<?php

use app\components\Helpers;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PaymentControlSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Posts');
$this->params['breadcrumbs'][] = $this->title;
$canUpdate = Yii::$app->user->can('posts-update');
$canView = Yii::$app->user->can('posts-view');
$canDelete = Yii::$app->user->can('posts-delete');
$canCreate = Yii::$app->user->can('posts-create');
$canDownload = Yii::$app->user->can('posts-xls');
?>
<div class="payment-control-index">
	<p class="pull-right">
		<?php
		if ($canCreate) {
      echo Html::a(Yii::t('app', 'btn-create'), ['create'],
                   [
                     'class' => 'btn btn-success btn-sm',
                     'style' => 'margin-right: 5px',
                     'data-step' => 2, 'data-intro' => Yii::t('intro', 'add-new-record')
                   ]);
		}
		if ($canDownload) {
			echo Html::a(
				Yii::t('app', 'btn-download'),
				['xls', 'PaymentControlSearch' => ($_GET['ReceptControlSearch'] ?? null)],
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
						'template' => '{view} {update} {delete} ',
						'header' => '<i class="fa fa-fw fa-gears"></i>',
						'headerOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
						'contentOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;'],
						'buttons' => [
              'view' => function($url, $model) use ($canView) {
                if(!$canView) return false;
                return Html::a(
                  '<span  class="glyphicon glyphicon-eye-open"></span>',
                  $url,
                  [
                    'title' => Yii::t('app', 'View')
                  ]
                );
              },
              'update' => function($url, $model) use ($canUpdate) {
                if(!$canUpdate) return false;
                return Html::a(
                  '<span  class="glyphicon glyphicon-pencil"></span>',
                  $url,
                  [
                    'title' => Yii::t('app', 'Update')
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
          'date',
          'material',
          'weight',
          'is_where',
          'comment',
				],
			]
		); ?>
	<?php Pjax::end(); ?>
</div>