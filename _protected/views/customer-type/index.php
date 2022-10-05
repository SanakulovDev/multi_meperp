<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomerTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Customer types');
$this->params['breadcrumbs'][] = $this->title;
$canUpdate = Yii::$app->user->can('customer-type-update');
$canDelete = Yii::$app->user->can('customer-type-delete');
?>
<div class="customer-type-index">


	<p class="pull-right">
		<? if (Yii::$app->user->can('customer-type-create')) { ?>
			<?= Html::a(
				Yii::t('app', 'btn-create'),
				['create'],
				[
					'class' => 'btn btn-success btn-sm',
					'data-intro' => Yii::t('intro', 'add-new-record')
				]
			) ?>
		<? } ?>
		<? if (Yii::$app->user->can('customer-type-create')) { ?>
			<?= Html::a(
				Yii::t('app', 'btn-download'),
				['xls', 'CustomerTypeSearch' => ($_GET['CustomerTypeSearch'] ?? null)],
				[
					'class' => 'btn btn-info btn-sm',
					'data-intro' => Yii::t('intro', 'download-button')
				]
			) ?>
		<? } ?>
	</p>
	<?php Pjax::begin(); ?>
	<?= GridView::widget([
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
			['class' => 'yii\grid\SerialColumn'],
			[
				'class' => 'yii\grid\ActionColumn',
				'template' => '{update}{delete}',
				'header' => '<i class="fa fa-fw fa-gears"></i>',
				'headerOptions' => ['style' => 'width: 80px;text-align: center;vertical-align:middle;color: #3c8dbc;'],
				'contentOptions' => ['style' => 'width: 80px;text-align: center;vertical-align:middle;'],
				'buttons' => [
					'update' => function ($url, $model) use ($canUpdate) {
						if (!$canUpdate) return false;
						$url = Url::toRoute(['customer-type/update', 'id' => $model->id]);
						return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', $url, [
							'title' => Yii::t('app', 'Edit')
						]) . '&nbsp;';
					},
					'delete' => function ($url, $model) use ($canDelete) {
						if (!$canDelete) return false;
						$url = Url::toRoute(['customer-type/delete', 'id' => $model->id]);
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
				'label' => Yii::t('app', 'Status'),
				'filter' => $searchModel->statusList,
				'attribute' => 'status',
				'contentOptions' => ['style' => 'text-align: center;'],
				'content' => function ($model, $column) {
					$sts_value = $model->status;
					switch ($sts_value) {
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
					$html = Html::tag('span', Html::encode($sts_name), ['class' => 'label label-' . $class]);
					return $sts_value === null ? $column->grid->emptyCell : $html;
				},
			],
			[
				'attribute' => 'created_at',
				'value' => 'updatedAtFormatted',
			],
			[
				'attribute' => 'created_by',
				'value' => 'createdBy.fullname',
			],
			[
				'attribute' => 'updated_at',
				'value' => 'updatedAtFormatted',
			],
			[
				'attribute' => 'updated_by',
				'value' => 'updatedBy.fullname',
			],
		],
	]); ?>

	<?php Pjax::end(); ?>
</div>