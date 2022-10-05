<?php
	use yii\grid\GridView;
	use yii\helpers\Html;

	/* @var $this yii\web\View */
	/* @var $searchModel app\models\ReqDetailSearch */
	/* @var $dataProvider yii\data\ActiveDataProvider */
	$this->title = Yii::t('app', 'Req Details');
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="req-detail-index">

	<h1><?=Html::encode($this->title)?></h1>
	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

	<p>
		<?=Html::a(Yii::t('app', 'Create Req Detail'), ['create'], ['class' => 'btn btn-success'])?>
	</p>

	<?=GridView::widget([
		                    'dataProvider' => $dataProvider,
		                    'filterModel' => $searchModel,
		                    'columns' => [
			                    ['class' => 'yii\grid\SerialColumn'],
			                    'req_id',
			                    'fromdate',
			                    'todate',
			                    'bal',
			                    'intransit',
			                    'plan',
			                    'qty',
		                    ],
	                    ]);?>
</div>
