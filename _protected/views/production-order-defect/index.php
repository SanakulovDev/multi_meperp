<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductionOrderDefectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Quality control');
$this->params['breadcrumbs'][] = $this->title;
$canDelete = Yii::$app->user->can('production-order-defect-delete');
$canCreate = Yii::$app->user->can('production-order-defect-create');
$canXls = Yii::$app->user->can('production-order-defect-xls');
?>
<?= $this->render('_search', ['model' => $searchModel]); ?>

<div class="production-order-defect-index">
		<p class="pull-right">
			<? if ($canCreate) { ?>
				<?= Html::a(Yii::t('app', 'btn-create'), ['create'], ['class' => 'btn btn-success btn-sm']) ?>
			<? } ?>
			<? if ($canXls) { ?>
				<?= Html::a(Yii::t('app', 'btn-download'), ['xls', 'PartPartSearch' => ($_GET['ProductionOrderDefectSearch'] ?? null)], ['class' => 'btn btn-info btn-sm']) ?>
			<? } ?>
		</p>

	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'summary' => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
		'options' => ['style' => 'overflow:auto;clear:both'],
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'class' => 'yii\grid\ActionColumn',
				'template' => '{delete} ',
				'header' => '<i class="fa fa-fw fa-gears"></i>',
				'headerOptions' => ['style' => 'width: 70px;text-align: center;vertical-align:middle;color: #3c8dbc;'],
				'contentOptions' => ['style' => 'width: 70px;text-align: center;vertical-align:middle;'],
				'buttons' => [
					'delete' => function ($url, $model) use($canDelete) {
                        if(!$canDelete) return false;
                        $url = Url::toRoute(['production-order-defect/delete', 'id' => $model->id]);
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
				'visible' => $canDelete
			],
			[
				'attribute' => 'production_order_id',
				'content' => function ($model) {
					return $model->productionOrder->serial_number;
				}
			],
			[
				'attribute' => 'defect_id',
				'content' => function ($model) {
					return $model->defect->code;
				}
			],
			[
				'attribute' => 'qty'
			],
			[
				'attribute' => 'created_by',
				'content' => function ($model) {
					return $model->createdBy->fullname;
				}
			],
			[
				'attribute' => 'created_at',
				'format' => ['date', 'php:d/m/Y H:i']
			],
		],
	]); ?>


</div>