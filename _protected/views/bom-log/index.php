<?php
	use yii\grid\GridView;
	use yii\widgets\Pjax;

	/* @var $this yii\web\View */
	/* @var $searchModel app\models\BomLogSearch */
	/* @var $dataProvider yii\data\ActiveDataProvider */
	$this->title = Yii::t('app', 'BOM history');
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bom-log-index">

	<?php Pjax::begin(); ?>

	<?=GridView::widget([
		                    'dataProvider' => $dataProvider,
		                    'filterModel' => $searchModel,
		                    'emptyText' => Yii::t('app', 'No results found.'),
		                    'summary' => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
		                    'options' => ['style' => 'overflow-x:scroll;clear:both'],
		                    'tableOptions' => [
			                    'style' => 'table-layout: fixed;', 'class' => 'sm_filter table table-striped table-bordered table-condensed table-sm-padding_2_0',
			                    'data-step' => 2,
			                    'data-intro' => Yii::t('intro', 'data-table')
		                    ],
		                    'filterRowOptions' => ['data-step' => 3, 'data-intro' => Yii::t('intro', 'filter')],
		                    'pager' => [
			                    'class' => '\yii\widgets\LinkPager',
			                    'options' => [
				                    'class' => 'pagination',
				                    'data-step' => 4,
				                    'data-intro' => Yii::t('intro', 'pagination')
			                    ],
		                    ],
		                    'columns' => [
			                    ['class' => 'yii\grid\SerialColumn'],
			                    'fullname',
			                    'subject',
			                    'action',
			                    'comment',
			                    'created_at:datetime',
		                    ],
	                    ]);?>
	<?php Pjax::end(); ?>
</div>
