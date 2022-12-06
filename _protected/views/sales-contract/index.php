<?php
	use app\models\ContractSubject;
	use app\models\Currency;
	use app\models\Customer;
	use app\models\PaymentTerm;
	use yii\grid\GridView;
	use yii\helpers\ArrayHelper;
	use yii\helpers\Html;
	use yii\helpers\Url;

	/* @var $this yii\web\View */
	/* @var $searchModel app\models\SalesContractSearch */
	/* @var $dataProvider yii\data\ActiveDataProvider */
	$this->title = Yii::t('app', 'Sales contracts');
	$this->params['breadcrumbs'][] = $this->title;
	$canUpdate = Yii::$app->user->can('sales-contract-update');
	$canDelete = Yii::$app->user->can('sales-contract-delete');
	$canCreate =  Yii::$app->user->can('sales-contract-create');
	$canXls = Yii::$app->user->can('sales-contract-xls');
?>
<div class="contract-index">

	<p class="pull-right">
		<? if ($canCreate) { ?>
		<?=Html::a(Yii::t('app', 'btn-create'), ['create'],
		           [
			           'class' => 'btn btn-success btn-sm',
			           'data-intro' => Yii::t('intro', 'add-new-record')
		           ])?>
				   <? } ?>
				   &nbsp;
		<? if ($canXls) { ?>
		<?=Html::a(Yii::t('app', 'btn-download'), ['xls', 'SalesContractSearch' => ($_GET['SalesContractSearch'] ?? null)],
		           [
			           'class' => 'btn btn-info btn-sm',
			           'data-intro' => Yii::t('intro', 'download-button')
		           ])?>
				   <? } ?>
	</p>

	<?=GridView::widget(
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
					'template' => '{update} {delete} ',
					'header' => '<i class="fa fa-fw fa-gears"></i>',
					'headerOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
					'contentOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;'],
					'buttons' => [
						'update' => function ($url, $model) use($canUpdate) {
							if(!$canUpdate) return false;
							$url = Url::toRoute(['sales-contract/update', 'id' => $model->id, 'update' => 1]);
							return Html::a('<span class="glyphicon  glyphicon-pencil" aria-hidden="true"></span>', $url, [
								'title' => Yii::t('app', 'Edit')
							]) . '&nbsp;';
						},
						'delete' => function ($url, $model) use($canDelete) {
							if(!$canDelete) return false;
							$url = Url::toRoute(['sales-contract/delete', 'id' => $model->id]);
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
					'headerOptions' => ['style' => 'width:auto;text-align: left;vertical-align:middle;'],
					'contentOptions' => ['style' => 'width:auto;text-align: left;vertical-align:middle;'],
					'content' => function($model){
						return Html::a($model->contract_no, Url::toRoute(['sales-contract/view', 'id' => $model->id]));
					},
				],
				[
					'attribute' => 'customer_id',
					'headerOptions' => ['style' => 'width:auto;text-align:center;vertical-align:middle;'],
					'contentOptions' => ['style' => 'width:auto;vertical-align:middle;'],
					'content' => function($model){
						return $model->customer->name;
					},
					'filter' => ArrayHelper::map(Customer::find()->all(), 'id', 'name'),
					'filterInputOptions' => [
						'class' => 'select2',
						'prompt' => Yii::t('app', '...'),
						'id' => null
					],
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
					'content' => function($model){
						return $model->contractSubject->name ?? null;
					},
					'filter' => ArrayHelper::map(contractSubject::find()
					                                            ->all(), 'id', 'name')
				],
				[
					'attribute' => 'currency_id',
					'headerOptions' => ['style' => 'width:auto;text-align: center;vertical-align:middle;'],
					'contentOptions' => ['style' => 'width:auto;text-align: center;vertical-align:middle;'],
					'content' => function($model){
						return $model->currency->code ?? null;
					},
					'filter' => ArrayHelper::map(Currency::find()->all(), 'id', 'code')
				],
				[
					'attribute' => 'payment_term_id',
					'headerOptions' => ['style' => 'width:auto;text-align: center;vertical-align:middle;'],
					'contentOptions' => ['style' => 'width:auto;text-align: center;vertical-align:middle;'],
					'content' => function($model){
						return $model->paymentTerm->name;
					},
					'filter' => ArrayHelper::map(paymentTerm::find()
					                                        ->all(), 'id', 'name')
				],
				[
					'attribute' => 'seller_id',
					'headerOptions' => ['style' => 'width:auto;text-align: center;vertical-align:middle;'],
					'contentOptions' => ['style' => 'width:auto;text-align: center;vertical-align:middle;'],
					'content' => function($model){
						return $model->seller->fullname;
					},
					'filter' => ArrayHelper::map(\app\models\User::find()
					                                             ->joinWith('role')
					                                             ->where(['item_name' => 'shipper'])
					                                             ->all(), 'id', 'fullname')
				],
				[
					'label' => Yii::t('app', 'Status'),
					'filter' => $searchModel->statusList,
					'attribute' => 'status',
					'headerOptions' => ['style' => 'text-align: center;vertical-align:middle;'],
					'contentOptions' => ['style' => 'text-align: center;'],
					'content' => function($model, $column){
						$sts_value = $model->status;
						switch($sts_value){
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
						$html = Html::tag('span', Html::encode($sts_name), ['class' => 'label label-'.$class]);
						return $sts_value === null ? $column->grid->emptyCell : $html;
					},
				],
				[
					'attribute' => 'created_at',
					'headerOptions' => ['style' => 'width:auto;text-align: center;vertical-align:middle;'],
					'value' => function($data){
						return date('d.m.Y (H:i:s)', $data->created_at);
					},
					'contentOptions' => function($model, $key, $index, $column){
						return [
							'style' => 'width:auto;text-align:center;vertical-align:middle;',
							'title' => (!empty($model->createdBy)) ? $model->createdBy->username : '-'
						];
					}
				],
			],
		]);?>
</div>
