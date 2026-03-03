<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DeliveryTermSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Delivery terms');
$this->params['breadcrumbs'][] = $this->title;
$canUpdate = Yii::$app->user->can('delivery-term-update');
$canDelete = Yii::$app->user->can('delivery-term-delete');
?>
<div class="delivery-term-index">

	<p class="pull-right">
		<? if (Yii::$app->user->can('delivery-term-create')) { ?>
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

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'emptyText' => Yii::t('app', 'No results found.'),
		'summary' => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
		'options' => ['style' => 'overflow-x:scroll;clear:both'],
		'tableOptions' => [
			'style' => 'table-layout: fixed;',
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
				'template' => '{update}{delete}',
				'header' => '<i class="fa fa-fw fa-gears"></i>',
				'headerOptions' => ['style' => 'width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
				'contentOptions' => ['style' => 'width:50px;text-align:center;vertical-align:middle;'],
				'buttons' => [
					'update' => function ($url, $model) use ($canUpdate) {
						if (!$canUpdate) return false;
						$url = Url::toRoute(['delivery-term/update', 'id' => $model->id]);
						return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', $url, [
							'title' => Yii::t('app', 'Edit')
						]) . '&nbsp;';
					},
					'delete' => function ($url, $model) use ($canDelete) {
						if (!$canDelete) return false;
						$url = Url::toRoute(['delivery-term/delete', 'id' => $model->id]);
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
			'priority',
			'description'
		],
	]); ?>
</div>