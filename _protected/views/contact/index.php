<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ContactSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Contacts');
$this->params['breadcrumbs'][] = $this->title;
$canUpdate = Yii::$app->user->can('contact-update');
$canDelete = Yii::$app->user->can('contact-delete');
?>
<div class="contact-index">

	
		<p class="pull-right">
			<? if (Yii::$app->user->can('contact-create')) { ?>
				<?= Html::a(
					Yii::t('app', 'btn-create'),
					['create'],
					[
						'class' => 'btn btn-success btn-sm form-modal',
						'data-step' => 2, 'data-intro' => Yii::t('intro', 'add-new-record')
					]
				) ?>
			<? } ?>
			
		</p>
	


	<?php Pjax::begin(['id' => 'pjaxGrid']); ?>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'summary' => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
		'options' => ['style' => 'overflow:auto;clear:both'],
		'tableOptions' => [
			'style' => 'table-layout: fixed;', 'class' => 'sm_filter table table-striped table-bordered table-condensed table-sm-padding_2_0',
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
				'headerOptions' => ['style' => 'width:50px;text-align:center;vertical-align:middle;'],
				'contentOptions' => ['style' => 'width:50px;text-align:center;vertical-align:middle;'],
			],
			[
				'class' => 'yii\grid\ActionColumn',
				'template' => '{update} {delete} ',
				'header' => '<i class="fa fa-fw fa-gears"></i>',
				'headerOptions' => ['style' => 'width:80px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
				'contentOptions' => ['style' => 'width:80px;text-align:center;vertical-align:middle;'],
				'buttons' => [
					'update' => function ($url, $model) use ($canUpdate) {
						if (!$canUpdate) return false;
						return Html::a(
							'<span  class="glyphicon glyphicon-pencil"></span>',
							false,
							[
								'class' => 'modalButtonUpdate',
								'value' => $url,
								'title' => Yii::t('app', 'Update')
							]
						);
					},
					'delete' => function ($url, $model) use ($canDelete) {
						if (!$canDelete) return false;
						return Html::a(
							'<span class="glyphicon glyphicon-trash"></span>',
							false,
							[
								'class' => 'modalButtonDelete',
								'data-href' => $url,
								'data-grid' => 'pjaxGrid',
								'title' => Yii::t('app', 'Delete')
							]
						);
					}
				],
				'visible' => $canUpdate || $canDelete
			],
			'name',
			'functionality',
			'department',
			'team',
			'responsibility',
			'mrp_code',
			'office_phone',
			'mobile_phone',
			'email:email',
			'mfu_code',
		],
	]); ?>

	<?php Pjax::end(); ?>
</div>