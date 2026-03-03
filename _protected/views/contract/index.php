<?php

use app\models\ContractSource;
use app\models\ContractSubject;
use app\models\Currency;
use app\models\PaymentTerm;
use app\models\Supplier;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ContractSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Contract Supplier');
$this->params['breadcrumbs'][] = $this->title;
$canUpdate = Yii::$app->user->can('contract-update');
$canDelete = Yii::$app->user->can('contract-delete');
$canView = Yii::$app->user->can('contract-view');

?>
<div class="contract-index">

	<p class="pull-right">
		<? if (Yii::$app->user->can('contract-create')) { ?>
			<?=
				Html::a(Yii::t('app', 'btn-create'), ['create'], [
					'class' => 'btn btn-success btn-sm',
					'data-intro' => Yii::t('intro', 'add-new-record')
				])
			?>
		<? } ?>
		&nbsp;
		<? if (Yii::$app->user->can('contract-xls')) { ?>
			<?=
				Html::a(Yii::t('app', 'btn-download'), ['xls', 'ContractSearch' => ($_GET['ContractSearch'] ?? null)], [
					'class' => 'btn btn-info btn-sm',
					'data-intro' => Yii::t('intro', 'download-button')
				])
			?>
		<? } ?>
	</p>

	<?=
		GridView::widget(
			[
				'dataProvider' => $dataProvider,
				'filterModel' => $searchModel,
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
				'summary' => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
				'options' => ['style' => 'overflow-x:scroll;clear:both'],
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
						'headerOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
						'contentOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;'],
						'buttons' => [
							'update' => function ($url, $model) use($canUpdate) {
								if(!$canUpdate) return false;
								$url = Url::toRoute(['contract/update', 'id' => $model->id]);
								return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', $url, [
									'title' => Yii::t('app', 'Edit')
								]) . '&nbsp;';
							},
							'delete' => function ($url, $model) use($canDelete) {
								if(!$canDelete) return false;
								$url = Url::toRoute(['contract/delete', 'id' => $model->id]);
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
						'attribute' => 'contract_no',
						'headerOptions' => ['style' => 'width:150px;text-align: left;vertical-align:middle;'],
						'contentOptions' => ['style' => 'width:150px;text-align: left;vertical-align:middle;'],
						'content' => function ($model) use($canView) {
							if($canView)
								return Html::a($model->contract_no, Url::toRoute(['contract/view', 'id' => $model->id]));
							else
								return $model->contract_no;

						},
					],
					[
						'attribute' => 'supplier_id',
						'headerOptions' => ['style' => 'width:auto;text-align:center;vertical-align:middle;'],
						'contentOptions' => ['style' => 'width:auto;vertical-align:middle;'],
						'content' => function ($model) {
							return $model->supplier->name;
						},
						//'filter' => ArrayHelper::map(Supplier::find()->all(), 'id', 'name')
						'filter' => Html::activeDropDownList($searchModel, 'supplier_id', ArrayHelper::map(Supplier::find()->all(), 'id', 'name'), ['class' => 'form-control select2', 'prompt' => '...']),
					],
					[
						'attribute' => 'contract_date',
						'headerOptions' => ['style' => 'width:auto;text-align: center;vertical-align:middle;'],
						'contentOptions' => ['style' => 'width:auto;text-align: center;vertical-align:middle;'],
					],
					[
						'attribute' => 'expiry_date',
						'headerOptions' => ['style' => 'width:auto;text-align: center;vertical-align:middle;'],
						'contentOptions' => ['style' => 'width:auto;text-align: center;vertical-align:middle;'],
					],
					[
						'attribute' => 'contract_subject_id',
						'headerOptions' => ['style' => 'width:auto;text-align: left;vertical-align:middle;'],
						'contentOptions' => ['style' => 'width:auto;text-align: left;vertical-align:middle;'],
						'content' => function ($model) {
							return $model->contractSubject->name;
						},
						'filter' => ArrayHelper::map(contractSubject::find()
							->all(), 'id', 'name')
					],
					[
						'attribute' => 'contract_source_id',
						'headerOptions' => ['style' => 'width:auto;text-align: center;vertical-align:middle;'],
						'contentOptions' => ['style' => 'width:auto;text-align: center;vertical-align:middle;'],
						'content' => function ($model) {
							return $model->contractSource->name;
						},
						'filter' => ArrayHelper::map(contractSource::find()
							->all(), 'id', 'name')
					],
					[
						'attribute' => 'currency_id',
						'headerOptions' => ['style' => 'width:auto;text-align: center;vertical-align:middle;'],
						'contentOptions' => ['style' => 'width:auto;text-align: center;vertical-align:middle;'],
						'content' => function ($model) {
							return $model->currency->code;
						},
						'filter' => ArrayHelper::map(currency::find()
							->all(), 'id', 'code')
					],
					[
						'attribute' => 'payment_term_id',
						'headerOptions' => ['style' => 'width:auto;text-align: center;vertical-align:middle;'],
						'contentOptions' => ['style' => 'width:auto;text-align: center;vertical-align:middle;'],
						'content' => function ($model) {
							return $model->paymentTerm->name;
						},
						'filter' => ArrayHelper::map(paymentTerm::find()
							->all(), 'id', 'name')
					],
					[
						'attribute' => 'buyer_id',
						'headerOptions' => ['style' => 'width:auto;text-align: center;vertical-align:middle;'],
						'contentOptions' => ['style' => 'width:auto;text-align: center;vertical-align:middle;'],
						'content' => function ($model) {
							return $model->buyer->fullname;
						},
						'filter' => ArrayHelper::map(\app\models\User::find()
							->joinWith('role')
							->where(['like','item_name','buyer%'])
							->all(), 'id', 'fullname')
					],
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
						'headerOptions' => ['style' => 'width:auto;text-align: center;vertical-align:middle;'],
						'value' => function ($data) {
							return date('d.m.Y (H:i:s)', $data->created_at);
						},
						'contentOptions' => function ($model, $key, $index, $column) {
							return [
								'style' => 'width:auto;text-align:center;vertical-align:middle;',
								'title' => (!empty($model->createdBy)) ? $model->createdBy->username : '-'
							];
						}
					],
				],
			]
		);
	?>
</div>