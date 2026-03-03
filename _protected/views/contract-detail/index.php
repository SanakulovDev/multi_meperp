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
	$this->title = Yii::t('app', 'Part contract');
	$this->params['breadcrumbs'][] = $this->title;

		
	$canUpdate = Yii::$app->user->can('contract-detail-update');
	$canDelete = Yii::$app->user->can('contract-detail-delete');
	$canView = Yii::$app->user->can('contract-view');
	
?>
<style>



</style>
<div class="contract-detail-index">

	<p class="pull-right">
		<? if (Yii::$app->user->can('contract-detail-create')) { ?>
			<?=Html::a(Yii::t('app', 'btn-create'), ['create'], ['class' => 'btn btn-success btn-sm', 'data-intro' => Yii::t('intro', 'add-new-record')])?>
		<?}?>
		<? if (Yii::$app->user->can('contract-detail-upload')) { ?>
			<?=Html::a(Yii::t('app', 'btn-upload-txt'), ['upload'], ['class' => 'btn btn-warning btn-sm', 'data-intro' => Yii::t('intro', 'add-upload')])?>
		<?}?>
		<? if (Yii::$app->user->can('contract-detail-download-template')) { ?>
			<?=Html::a(Yii::t('app', 'btn-download-template'), '/public/contract_details_template.xlsx', ['class' => 'btn btn-info btn-sm', 'data-intro' => Yii::t('intro', 'btn-download-template')])?>
		<?}?>
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
				'rowOptions' => ['style' => 'height: 25px;'],
				'options' => ['style' => 'overflow:auto;clear:both'],
				'columns' => [
					[
						'class' => 'yii\grid\SerialColumn',
						'header' => '№',
						'headerOptions' => ['style' => 'width:30px;text-align: center;color: #3c8dbc;'],
						'contentOptions' => ['style' => 'text-align: center;padding: 0px 0px 0px 0px !important;']
					],
					[
						'class' => 'yii\grid\ActionColumn',
						'template' => '{update}{delete}',
						'header' => '<i class="fa fa-fw fa-gears"></i>',
						'headerOptions' => ['style' => 'width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
						'contentOptions' => ['style' => 'width:50px;text-align:center;vertical-align:middle;padding: 0px 0px 0px 0px !important;'],
						'buttons' => [
							'update' => function ($url, $model) use($canUpdate) {
								if(!$canUpdate) return false;
								$url = Url::toRoute(['contract-detail/update', 'id' => $model->id]);
								return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', $url, [
									'title' => Yii::t('app', 'Edit')
								]) . '&nbsp;';
							},
							'delete' => function ($url, $model) use($canDelete) {
								if(!$canDelete) return false;
								$url = Url::toRoute(['contract-detail/delete', 'id' => $model->id]);
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
						'attribute' => 'part_no',
						'headerOptions' => ['style' => 'width:120px;text-align: left;vertical-align:middle;'],
						'contentOptions' => ['style' => 'text-align: left;vertical-align:middle;padding: 0px 0px 0px 0px !important;'],
						'content' => function($model){
							return $model->part->part_no;
						},
					],
					[
						'attribute' => 'part_name',
						'headerOptions' => ['style' => 'vertical-align:middle;'],
						'contentOptions' => ['style' => 'vertical-align:middle;max-width:120px;padding: 0px 0px 0px 0px !important;', 'class' => 'td-nowrap'],
						'content' => function($model){
							return '<span title="' . $model->part->part_name . '">' . $model->part->part_name . '</span>';
						},
					],
          [
            'attribute' => 'part_color',
            'headerOptions' => ['style' => 'text-align: left;vertical-align:middle;width:100px'],
            'contentOptions' => ['style' => 'text-align: left;vertical-align:middle;width:100px;overflow:hidden;padding: 0px 0px 0px 0px !important;', 'class' => 'td-nowrap'],
            'content' => function($model){
              return '<span title="' . $model->part->part_color . '">' . $model->part->part_color . '</span>';
            },
          ],
					[
						'attribute' => 'contract_id',
						'headerOptions' => ['style' => 'width:270px;text-align: left;vertical-align:middle;'],
						'contentOptions' => ['style' => 'text-align: left;vertical-align:middle;max-width:270px;padding: 0px 0px 0px 0px !important;', 'class' => 'td-nowrap'],
						'content' => function($model) use($canView) {
							if($canView)
								return Html::a($model->contract->contract_no, Url::toRoute(['contract/view', 'id' => $model->contract->id]),[
									'title' => $model->contract->contract_no. ' | ' . $model->contract->statusText. ' | ' . $model->contract->contract_date. ' <> ' . $model->contract->expiry_date
								]);
							else
								return '<span title="' . $model->contract->contract_no. ' | ' . $model->contract->statusText. ' | ' . $model->contract->contract_date. ' <> ' . $model->contract->expiry_date . '">' . $model->contract->contract_no . '</span>';
						},
					],
					[
						'attribute' => 'delivery_term_id',
						'headerOptions' => ['style' => 'width:100px;text-align: center;vertical-align:middle;'],
						'contentOptions' => ['style' => 'width:100px;text-align: center;vertical-align:middle;padding: 0px 0px 0px 0px !important;'],
						'content' => function($model){
							return $model->deliveryTerm->name ?? null;
						},
						'filter' => ArrayHelper::map(deliveryTerm::find()
						                                         ->all(), 'id', 'name')
					],
					[
						'attribute' => 'price',
						'headerOptions' => ['style' => 'width:100px;text-align: right;vertical-align:middle;'],
						'contentOptions' => ['style' => 'width:100px;text-align: right;vertical-align:middle;padding: 0px 0px 0px 0px !important;'],
						'content' => function($model){
							return Helpers::formatRemoveDecimal($model->price);
						},
					],
					[
						'attribute' => 'is_primary_price',
						'filter' => [ 0 => Yii::t('app', 'No'), 1 => Yii::t('app', 'Yes')],
						'headerOptions' => ['style' => 'width:90px;text-align: center;vertical-align:middle;'],
						'contentOptions' => ['style' => 'width:90px;text-align: center;height: 25px;padding: 0px 0px 0px 0px !important;'],
						'content' => function ($model, $column) {
							$is_primary_price = $model->is_primary_price;
							switch ($is_primary_price) {
								case 1:
									$class = 'success';
									$name = "✔";
									$title = Yii::t('app', 'Remove selection');
									break;
								case 0:
									$class = 'danger';
									$name = "✖";
									$title = Yii::t('app', 'Select as primary');
									break;
							}
							if($model->contract->isActive){
								return Html::a(Html::encode($name), ['contract-detail/set-primary-price', 'id' => $model->id], ['class' => 'label label-' . $class,'title' => $title]);
							}else{
								return Html::tag('span', Html::encode($name), ['class' => 'label label-default', 'title' => Yii::t('app', 'You cannot set this price as primary.')]);
							}
							
						},
					],
					[
						'attribute' => 'cnfea',
						'headerOptions' => ['style' => 'width:100px;text-align: center;vertical-align:middle;'],
						'contentOptions' => ['style' => 'width:100px;text-align: center;vertical-align:middle;padding: 0px 0px 0px 0px !important;'],
					],
					// [
					// 	'attribute' => 'weekly_capacity',
					// 	'headerOptions' => ['style' => 'width:100px;text-align: right;vertical-align:middle;'],
					// 	'contentOptions' => ['style' => 'width:100px;text-align: right;vertical-align:middle;'],
					// 	'content' => function($model){
					// 		return Helpers::formatRemoveDecimal($model->weekly_capacity);
					// 	},
					// ],
					[
						'attribute' => 'sub_source',
						'headerOptions' => ['style' => 'width:100px;text-align: center;vertical-align:middle;'],
						'contentOptions' => ['style' => 'width:100px;text-align: center;vertical-align:middle;padding: 0px 0px 0px 0px !important;'],
						'content' => function($model){
							return $model->subSourceText;
						},
						'filter' => $searchModel->subSourceList
					],
					[
						'attribute' => 'lead_time',
						'headerOptions' => ['style' => 'width:120px;text-align: center;vertical-align:middle;'],
						'contentOptions' => ['style' => 'width:120px;text-align: center;vertical-align:middle;padding: 0px 0px 0px 0px !important;'],
					]
				],
			]
		);
	?>
</div>
