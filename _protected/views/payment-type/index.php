<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PaymentTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Payment types');
$this->params['breadcrumbs'][] = $this->title;
$canUpdate = Yii::$app->user->can('payment-type-update');
$canDelete = Yii::$app->user->can('payment-type-delete');
$canView = Yii::$app->user->can('payment-type-view');
$canCreate = Yii::$app->user->can('payment-type-create');
?>
<div class="payment-type-index">

	<p class="pull-right">
		<? if ($canCreate) { ?>
			<?= Html::a(
				Yii::t('app', 'btn-create'),
				['create'],
				[
					'class' => 'btn btn-success btn-sm',
					'data-intro' => Yii::t('intro', 'add-new-record')
				]
			) ?>
		<? } ?>
	</p>

	<?php Pjax::begin(); ?>
	<?php // echo $this->render('_search', ['model' => $searchModel]); 
	?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'summary' => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
		'options' => ['style' => 'overflow:auto;clear:both'],
		'emptyText' => Yii::t('app', 'No results found.'),
		'tableOptions' => [
			'class' => 'table table-striped table-bordered table-condensed table-sm-padding_2_0',
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
				'template' => '{view} {update} {delete} ',
				'header' => '<i class="fa fa-fw fa-gears"></i>',
				'headerOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
				'contentOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;'],
				'buttons' => [
					'view' => function ($url, $model) use ($canView) {
						if (!$canView) return false;
						$url = Url::toRoute(['payment-type/view', 'id' => $model->id]);
						return Html::a('<span class="glyphicon glyphicon-eye" aria-hidden="true"></span>', $url, [
							'title' => Yii::t('app', 'View')
						]) . '&nbsp;';
					},
					'update' => function ($url, $model) use ($canUpdate) {
						if (!$canUpdate) return false;
						$url = Url::toRoute(['payment-type/update', 'id' => $model->id]);
						return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', $url, [
							'title' => Yii::t('app', 'Edit')
						]) . '&nbsp;';
					},
					'delete' => function ($url, $model) use ($canDelete) {
						if (!$canDelete) return false;
						$url = Url::toRoute(['payment-type/delete', 'id' => $model->id]);
						return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>', $url, [
							'title' => Yii::t('app', 'Delete'),
							'data' => [
								'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
								'method' => 'post',
							],
						]) . '&nbsp;';
					},
				],
				'visible' => $canView || $canUpdate || $canDelete
			],
			'id',
			'title',
			'description',
			// 'created_by',
			//			                    [
			//				                    'attribute' => 'created_at',
			//				                    'content' => function($model){ return $model->createdAtFormatted; }
			//			                    ],
			//			                    [
			//				                    'attribute' => 'updated_at',
			//				                    'content' => function($model){ return $model->updatedAtFormatted; }
			//			                    ],
			//'updated_by',
		],
	]); ?>

	<?php Pjax::end(); ?>

</div>