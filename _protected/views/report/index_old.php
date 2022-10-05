<?php
	use yii\grid\GridView;
	use yii\helpers\Html;
	use yii\helpers\Url;
	use yii\widgets\Pjax;

	/* @var $this yii\web\View */
	/* @var $searchModel app\models\ReportSearch */
	/* @var $dataProvider yii\data\ActiveDataProvider */
	$this->title = Yii::t('app', 'Report lists');
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="report-index">
  
  <div class="btn-group" style="width: 100%;">
  <a href="<?=Url::toRoute(['report/index-boxes'])?>" class="btn btn-default pull-right" title="<?=Yii::t('app', 'Grid view')?>"><i class="fa fa-th"></i></a>
  <a href="<?=Url::toRoute(['report/index'])?>" class="btn btn-default pull-right btn-default disabled" title="<?=Yii::t('app', 'List view')?>"><i class="fa fa-list"></i></a>
</div>


	<?php Pjax::begin(); ?>

	<?
		echo GridView::widget(
			[
				'dataProvider' => $dataProvider,
				'filterModel' => $searchModel,
				'emptyText' => Yii::t('app', 'No results found.'),
				'summary' => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
				'options' => ['style' => 'overflow-x:auto;clear:both'],
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
				'columns' => [
					[
						'class' => 'yii\grid\SerialColumn',
						'header' => '№',
						'headerOptions' => ['style' => 'width: 40px;text-align: center;color: #3c8dbc;'],
						'contentOptions' => ['style' => 'width: 40px;text-align: center;']
					],
					[
						'attribute' => 'title',
						'headerOptions' => ['style' => 'width: 300px;vertical-align:middle;color: #3c8dbc;'],
						'contentOptions' => ['style' => 'width: 300px;vertical-align:middle;'],
						'content' => function($model){
							if(count($model->userReports) > 0){
								return Html::a($model->title, Url::toRoute(['report/'.$model->action]), ['target' => '_blank', 'data-pjax' => 0]);
							}else{
								return $model->title;
							}
						},
					],
					[
						'attribute' => 'description',
						'content' => function($model){
              return Yii::t('app', $model->description);
            },
					],
				],
			]
		); ?>
	<?php Pjax::end(); ?>
</div>
