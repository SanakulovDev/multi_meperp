<?php

use app\models\Part;
use app\models\ProductSpecification;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductSpecificationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Product specification');
$this->params['breadcrumbs'][] = $this->title;

$canUpdate = Yii::$app->user->can('product-specification-update');
$canDelete = Yii::$app->user->can('product-specification-delete');
$canActivate = Yii::$app->user->can('product-specification-activate');
$canDuplicate = Yii::$app->user->can('product-specification-duplicate');

$notRawParts = ArrayHelper::map(Part::find()->where(['state' => [Part::STATE_SEMI, Part::STATE_FINISHED]])->all(), 'id', 'partinfo');
?>
<div class="product-parts-index">

    <p class="pull-right">
        <?php
		if (Yii::$app->user->can('product-specification-create')) {
			echo Html::a(
				Yii::t('app', 'btn-create'),
				['create'],
				[
					'class' => 'btn btn-success btn-sm',
					'style' => 'margin-right: 5px',
					'data-intro' => Yii::t('intro', 'add-new-record')
				]
			);
		}
		if (Yii::$app->user->can('bom-log-index')) {
			echo Html::a(
				Yii::t('app', 'btn-history'),
				['bom-log/index'],
				[
					'class' => 'btn btn-warning btn-sm',
					'style' => 'margin-right: 5px',
					'data-intro' => Yii::t('intro', 'history')
				]
			);
		}
		if (Yii::$app->user->can('product-specification-xls')) {
			echo Html::a(
				Yii::t('app', 'btn-download'),
				['xls', 'ProductSpecificationSearch' => ($_GET['ProductSpecificationSearch'] ?? null)],
				[
					'class' => 'btn btn-info btn-sm m-r searchPjax',
					'style' => 'margin-right: 5px',
					'data-intro' => Yii::t('intro', 'download-button')
				]
			);
		} ?>
    </p>
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
    			'header' => '#',
    			'headerOptions' => ['style' => 'width: auto;text-align: center;color: #3c8dbc;'],
    			'contentOptions' => ['style' => 'width: auto;text-align: center;']
    		],
    		[
    			'class' => 'yii\grid\ActionColumn',
    			'template' => '{activate} {duplicate} {update} {delete} ',
    			'header' => '<i class="fa fa-fw fa-gears"></i>',
    			'headerOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
    			'contentOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;'],
    			'buttons' => [
						'activate' => function ($url, $model) use ($canActivate) {
    					if (!$canActivate || $model->status == ProductSpecification::STATUS_ACTIVE) {
    						return false;
    					}
    					return Html::a(
    						'<span  class="glyphicon glyphicon-ok-circle"></span>',
    						$url,
    						[
    							'title' => Yii::t('app', 'Activate')
    						]
    					);
    				},
						'duplicate' => function ($url, $model) use ($canDuplicate) {
    					if (!$canDuplicate) {
    						return false;
    					}
    					return Html::a(
    						'<span  class="glyphicon glyphicon-duplicate"></span>',
    						$url,
    						[
    							'title' => Yii::t('app', 'Duplicate')
    						]
    					);
    				},
						'update' => function ($url, $model) use ($canUpdate) {
    					if (!$canUpdate || ($model->productionOrderCount && $model->productionOrderCount[0]['counted'] > 0)) {
    						return false;
    					}
    					return Html::a(
    						'<span  class="glyphicon glyphicon-pencil"></span>',
    						$url,
    						[
    							'title' => Yii::t('app', 'Update')
    						]
    					);
    				},
    				'delete' => function ($url, $model) use ($canDelete) {
    					if (!$canDelete || ($model->productionOrderCount && $model->productionOrderCount[0]['counted'] > 0)) {
    						return false;
    					}
    					return Html::a(
    						'<span class="glyphicon glyphicon-trash"></span>',
    						$url,
    						[
									'data-confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                  'data-method' => 'post',
    							'title' => Yii::t('app', 'Delete')
    						]
    					);
    				}
    			],
    			'visible' => $canDelete || $canUpdate
    		],
				// 'id',
				[
					'attribute' => 'code',
					'content' => function($model) {
						$color = 'text-black';
						switch ($model->part->state) {
							case Part::STATE_FINISHED:
								$color = 'text-success';
								break;
							case Part::STATE_SEMI:
								$color = 'text-primary';
								break;
							}
						return Html::a($model->code, ['view', 'id'=>$model->id], ['class' => $color]);
					}
				],
				[
					'attribute' => 'part_id',
					'headerOptions' => ['style' => 'width: 200px;text-align: left;vertical-align:middle;'],
					'filter' => Html::activeDropDownList($searchModel, 'part_id', $notRawParts, ['class' => 'form-control select2', 'prompt' => '...']),
					'contentOptions' => ['class' => 'td-nowrap'],	
					'content' => function ($model) {
							return $model->part->partinfo;
					},
				],
				[
    			'attribute' => 'amount',
					'headerOptions' => ['style' => 'width: 100px;text-align: right;vertical-align:middle;'],
					'contentOptions' => ['style' => 'text-align: right;vertical-align:middle;'],
					'value' => function($model) { return $model->amount + 0; }
				],
				[
					'attribute' => 'part_nm',
					'headerOptions' => ['style' => 'width: 200px;text-align: left;vertical-align:middle;color: #3c8dbc;'],
					'contentOptions' => function ($model, $column) {
						return ['title' => $model->part->part_name ?? null, 'class' => 'td-nowrap', 'style' => 'max-width:200px'];
					},
					'content' => function ($model) {
						return $model->part->part_name;
					}
				],
				'description',
    		[
    			'filter' => $searchModel->statusList(),
    			'attribute' => 'status',
    			'headerOptions' => ['style' => 'width: 100px;text-align: center;vertical-align:middle;'],
    			'contentOptions' => ['style' => 'width: 100px;text-align: center;vertical-align:middle;'],
    			'content' => function ($model, $column) {
    				$sts_value = $model->status;
    				switch ($sts_value) {
    				case 1:
    				  $class = 'success';
    				  $sts_name = '✔';
    				  $sts_title = Yii::t('app', 'Active');
    				  break;
    				case 0:
    				  $class = 'danger';
    				  $sts_name = '✖';
    				  $sts_title = Yii::t('app', 'Inactive');
    				  break;
    			  }
    				$html = Html::tag('span', Html::encode($sts_name), ['title' => $sts_title, 'class' => 'label label-' . $class]);
    				return $sts_value === null ? $column->grid->emptyCell : $html;
    			},
    		],
    	],
    ]); ?>

</div>
<?= $this->render('/part-part/_modal-bom-collapse.php') ?>