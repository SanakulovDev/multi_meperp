<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DefectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Defects');
$this->params['breadcrumbs'][] = $this->title;
$canUpdate = Yii::$app->user->can('defect-update');
$canDelete = Yii::$app->user->can('defect-delete');
?>
<div class="defect-index">

	<p class="pull-right">
		<? if (Yii::$app->user->can('defect-create')) { ?>
			<?= Html::a(
				Yii::t('app', 'btn-create'),
				['create'],
				[
					'class' => 'btn btn-success btn-sm',
					'data-intro' => Yii::t('intro', 'add-new-record')
				]
			) ?>
		<? } ?>
		<? if (Yii::$app->user->can('defect-xls')) { ?>
			<?= Html::a(
				Yii::t('app', 'btn-download'),
				['xls', 'DefectSearch' => ($_GET['DefectSearch'] ?? null)],
				[
					'class' => 'btn btn-info btn-sm',
					'data-intro' => Yii::t('intro', 'download-button')
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
				'header' => '№',
				'headerOptions' => ['style' => 'width: 40px;text-align: center;color: #3c8dbc;'],
				'contentOptions' => ['style' => 'width: 40px;text-align: center;']
			],
			[
				'class' => 'yii\grid\ActionColumn',
				'template' => '{update}{delete}',
				'header' => '<i class="fa fa-fw fa-gears"></i>',
				'headerOptions' => ['style' => 'width:80px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
				'contentOptions' => ['style' => 'width:80px;text-align:center;vertical-align:middle;'],
				'buttons' => [
					'update' => function ($url, $model) use ($canUpdate) {
						if (!$canUpdate) return false;
						$url = Url::toRoute(['defect/update', 'id' => $model->id]);
						return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', $url, [
							'title' => Yii::t('app', 'Edit')
						]) . '&nbsp;';
					},
					'delete' => function ($url, $model) use ($canDelete) {
						if (!$canDelete) return false;
						$url = Url::toRoute(['defect/delete', 'id' => $model->id]);
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
			[
				'attribute' => 'category',
				'value' => 'categoryText',
        "filter" => Html::activeDropDownList($searchModel, "category", $searchModel->categoryList(), [
          "class" => "form-control",
          "prompt" => "...",
        ]),
			],
      [
        'attribute' => 'code',
        'headerOptions' => ['style' => 'width: 250px;vertical-align:middle;'],
        'contentOptions' => ['style' => 'width: 250px;vertical-align:middle;'],
      ],
			'description',
		],
	]); ?>


</div>