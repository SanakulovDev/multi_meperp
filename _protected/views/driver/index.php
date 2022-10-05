<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DriverSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Drivers');
$this->params['breadcrumbs'][] = $this->title;
$canUpdate = Yii::$app->user->can('driver-update');
$canDelete = Yii::$app->user->can('driver-delete');
?>
<div class="driver-index">
	<p class="pull-right">
		<? if (Yii::$app->user->can('driver-create')) { ?>
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

	<?= GridView::widget(
		[
			'dataProvider' => $dataProvider,
			'filterModel' => $searchModel,
			'emptyText' => Yii::t('app', 'No results found.'),
			'summary' => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
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
			'options' => ['style' => 'overflow:auto;clear:both'],
			'columns' => [
				[
					'class' => 'yii\grid\SerialColumn',
					'header' => '№',
					'headerOptions' => ['style' => 'width: auto;text-align: center;color: #3c8dbc;'],
					'contentOptions' => ['style' => 'width: auto;text-align: center;']
				],
				[
					'class' => 'yii\grid\ActionColumn',
					'template' => '{update} {delete} ',
					'header' => '<i class="fa fa-fw fa-gears"></i>',
					'headerOptions' => ['style' => 'width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
					'contentOptions' => ['style' => 'width:50px;text-align:center;vertical-align:middle;'],
					'buttons' => [
						'update' => function ($url, $model) use ($canUpdate) {
							if (!$canUpdate) return false;
							$url = Url::toRoute(['driver/update', 'id' => $model->id]);
							return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', $url, [
								'title' => Yii::t('app', 'Edit')
							]) . '&nbsp;';
						},
						'delete' => function ($url, $model) use ($canDelete) {
							if (!$canDelete) return false;
							$url = Url::toRoute(['driver/delete', 'id' => $model->id]);
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
				'last_name',
				'first_name',
				'middle_name',
				'emp_no',
				[
					'attribute' => 'created_at',
					'value' => function ($data) {
						return date('d.m.Y (H:i:s)', $data->created_at);
					},
					'contentOptions' => function ($model, $key, $index, $column) {
						return ['title' => (!empty($model->createdBy)) ? $model->createdBy->username : '-'];
					}
				],
				[
					'attribute' => 'updated_at',
					'value' => function ($data) {
						$update_val = date('d.m.Y (H:i:s)', $data->updated_at);
						return $update_val;
					},
					'contentOptions' => function ($model, $key, $index, $column) {
						return ['title' => (!empty($model->updatedBy)) ? $model->updatedBy->username : '-'];
					}
				],
			],
		]
	); ?>

	<?php Pjax::end(); ?>

</div>