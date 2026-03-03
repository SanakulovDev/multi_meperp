<?php

use app\components\Helpers;
use app\models\DeliveryTerm;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ContractDetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'FG contract');
$this->params['breadcrumbs'][] = $this->title;
$canUpdate = Yii::$app->user->can('sales-contract-detail-update');
$canDelete = Yii::$app->user->can('sales-contract-detail-delete');
$canCreate =  Yii::$app->user->can('sales-contract-detail-create');
?>
<div class="contract-detail-index">

	<p class="pull-right">
		<?php if ($canCreate) { ?>
			<?= Html::a(Yii::t('app', 'btn-create'), ['create'], ['class' => 'btn btn-success btn-sm', 'data-intro' => Yii::t('intro', 'add-new-record')]) ?>
		<?php } ?>
		<?= Html::a(Yii::t('app', 'btn-upload-txt'), ['upload'], ['class' => 'btn btn-warning btn-sm', 'data-intro' => Yii::t('intro', 'add-upload')]) ?>
		<?= Html::a(Yii::t('app', 'btn-download-template'), '/public/sales_contract_details_template.xlsx', ['class' => 'btn btn-info btn-sm', 'data-intro' => Yii::t('intro', 'btn-download-template')]) ?>
	</p>

	<?=
		GridView::widget(
			[
				'dataProvider' => $dataProvider,
				'filterModel' => $searchModel,
				'summary' => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
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
				'options' => ['style' => 'overflow:auto;clear:both'],
				'columns' => [
					[
						'class' => 'yii\grid\SerialColumn',
						'header' => '№',
						'headerOptions' => ['style' => 'width:30px;text-align: center;color: #3c8dbc;'],
						'contentOptions' => ['style' => 'text-align: center;']
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
								$url = Url::toRoute(['sales-contract-detail/update', 'id' => $model->id]);
								return Html::a('<span class="glyphicon  glyphicon-pencil" aria-hidden="true"></span>', $url, [
									'title' => Yii::t('app', 'Edit')
								]) . '&nbsp;';
							},
							'delete' => function ($url, $model) use($canDelete) {
								if(!$canDelete) return false;
								$url = Url::toRoute(['sales-contract-detail/delete', 'id' => $model->id]);
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
						//'visible' => Yii::$app->user->can('pe') or Yii::$app->user->can('buyer')
					],
					[
						'attribute' => 'sales_contract_id',
						'headerOptions' => ['style' => 'width:150px;text-align: left;vertical-align:middle;'],
						'contentOptions' => ['style' => 'text-align: left;vertical-align:middle;'],
						'content' => function ($model) {
							return Html::a($model->salesContract->contract_no, Url::toRoute(['sales-contract/view', 'id' => $model->sales_contract_id]));
						},
					],
					[
						'attribute' => 'delivery_term_id',
						'headerOptions' => ['style' => 'width:100px;text-align: center;vertical-align:middle;'],
						'contentOptions' => ['style' => 'width:100px;text-align: center;vertical-align:middle;'],
						'content' => function ($model) {
							return $model->deliveryTerm->name ?? null;
						},
						'filter' => ArrayHelper::map(deliveryTerm::find()
							->all(), 'id', 'name')
					],
					[
						'attribute' => 'part_no',
						'headerOptions' => ['style' => 'width:120px;text-align: left;vertical-align:middle;'],
						'contentOptions' => ['style' => 'text-align: left;vertical-align:middle;'],
						'content' => function ($model) {
							return $model->part->part_no;
						},
					],
					[
						'attribute' => 'part_name',
						'headerOptions' => ['style' => 'vertical-align:middle;'],
						'contentOptions' => ['style' => 'vertical-align:middle;'],
						'content' => function ($model) {
							return $model->part->part_name;
						},
					],
          [
            'attribute' => 'part_color',
            'headerOptions' => ['style' => 'vertical-align:middle;'],
            'contentOptions' => ['style' => 'vertical-align:middle;'],
            'content' => function ($model) {
              return $model->part->part_color;
            },
          ],
					[
						'attribute' => 'price',
						'headerOptions' => ['style' => 'text-align: right;vertical-align:middle;'],
						'contentOptions' => ['style' => 'text-align: right;vertical-align:middle;'],
						'content' => function ($model) {
							return Helpers::formatRemoveDecimal($model->price);
						},
					],
					[
						'attribute' => 'vat',
						'headerOptions' => ['style' => 'text-align: right;vertical-align:middle;'],
						'contentOptions' => ['style' => 'text-align: right;vertical-align:middle;'],
						'content' => function ($model) {
							return number_format($model->vat, 2);
						},
					],
					[
						'attribute' => 'excise',
						'headerOptions' => ['style' => 'text-align: right;vertical-align:middle;'],
						'contentOptions' => ['style' => 'text-align: right;vertical-align:middle;'],
						'content' => function ($model) {
							return number_format($model->excise, 2);
						},
					],

				],
			]
		);
	?>
</div>