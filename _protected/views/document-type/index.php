<?php
	use yii\grid\GridView;
	use yii\widgets\Pjax;

	/* @var $this yii\web\View */
	/* @var $searchModel app\models\DocumentTypeSearch */
	/* @var $dataProvider yii\data\ActiveDataProvider */
	$this->title = Yii::t('app', 'Document types');
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-type-index">
	<?php Pjax::begin(); ?>
	<p class="pull-right">
		<? //=Html::a(Yii::t('app', 'Create document type'), ['create'], ['class' => 'btn btn-success'])?>
	</p>

	<?=GridView::widget([
		                    'dataProvider' => $dataProvider,
		                    'filterModel' => $searchModel,
		                    'summary' => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
		                    'options' => ['style' => 'overflow:auto;clear:both'],
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
		                    'columns' => [
			                    ['class' => 'yii\grid\SerialColumn'],
			                    'code',
			                    'name',
			                    'description:ntext',
			                    'yyyy',
			                    'sequence',
		                    ],
	                    ]);?>
	<?php Pjax::end(); ?>
</div>
