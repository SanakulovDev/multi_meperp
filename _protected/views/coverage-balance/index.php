<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CoverageBalanceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Cash requirement for import shipments');
$this->params['breadcrumbs'][] = $this->title;

$canUpdate = Yii::$app->user->can('coverage-balance-update');

?>
<style>

	.modalButtonUpdate:hover{
		cursor: pointer;
	}
</style>
<div class="coverage-balance-index">
	<?php Pjax::begin(['id' => 'pjaxGrid']); ?>
    <?= GridView::widget([
    	'dataProvider' => $dataProvider,
    	'filterModel' => $searchModel,
    	'summary' => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
    	'options' => ['style' => 'overflow:auto;clear:both'],
    	'emptyText' => Yii::t('app', 'No results found.'),
    	'tableOptions' => [
    		'class' => 'sm_filter table table-striped table-bordered table-condensed table-sm-padding_2_0',
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
    			'header' => '№',
    			'headerOptions' => ['style' => 'width: 50px;text-align: center;color: #3c8dbc;'],
    			'contentOptions' => ['style' => 'width: 50px;text-align: center;']
    		],
				[
					'attribute' => 'country',
    			'headerOptions' => ['style' => 'width:250px;text-align:left;vertical-align:middle;color: #3c8dbc;'],
    			'contentOptions' => ['style' => 'width:250px;text-align:left;vertical-align:middle;'],
					'value' => 'supplier.countryCode.name',
					'filter' => Html::activeDropDownList($searchModel, 'country', $countries, ['class' => 'form-control select2', 'prompt' => '...']),
				],
				[
					'attribute' => 'supplier_id',
					'value' => 'supplier.name',
					'filter' => Html::activeDropDownList($searchModel, 'supplier_id', $suppliers, ['class' => 'form-control select2', 'prompt' => '...']),
				],
				[
					'attribute' => 'payment_term_id',
    			'headerOptions' => ['style' => 'width:150px;text-align:center;vertical-align:middle;'],
    			'contentOptions' => ['style' => 'width:150px;text-align:center;vertical-align:middle;'],
					'content' => function($model){
						return $model->paymentTerm->name . ' (' . $model->currency->code . ')';
					},
					'filter' => Html::activeDropDownList($searchModel, 'payment_term_id', $paymentTerms, ['class' => 'form-control select2', 'prompt' => '...']),
				],
				[
					'attribute' => 'period',
    			'headerOptions' => ['style' => 'width:150px;text-align:center;vertical-align:middle;'],
    			'contentOptions' => ['style' => 'width:150px;text-align:center;vertical-align:middle;'],
					'value' => 'periodMonth',
					'filter' =>false
				],
				[
					'attribute' => 'debt',
    			'headerOptions' => ['style' => 'width:150px;text-align:right;vertical-align:middle;'],
					'contentOptions' => ['style' => 'width:150px;text-align:right;vertical-align:middle;'],
					'content' => function($model) {
						return number_format(round($model->debt,0),0,'.',' ');
					}
				],
				[
					'attribute' => 'paid',
    			'headerOptions' => ['style' => 'width:150px;text-align:right;vertical-align:middle;'],
					'contentOptions' => ['style' => 'width:150px;text-align:right;vertical-align:middle;'],
					'content' => function ($model) use ($canUpdate){
						$paid = number_format(round($model->paid,0),0,'.',' ');
						if (!$canUpdate) return $paid;
						$url = Url::toRoute(['update', 'id' => $model->id]);
						return Html::a(
							$paid,
							false,
							[
									'class' => 'modalButtonUpdate',
									'value' => $url,
									'title' => Yii::t('app', 'Update')
							]
						);
					}
				],
				[
					'attribute' => 'diff',
    			'headerOptions' => ['style' => 'width:150px;text-align:right;vertical-align:middle;color: #3c8dbc;'],
					'contentOptions' => ['style' => 'width:150px;text-align:right;vertical-align:middle;'],
					'content' => function($model) {
						return number_format(round($model->diff,0),0,'.',' ');
					}
				],
    	],
    ]); ?>

    <?php Pjax::end(); ?>

</div>