<?php
	use yii\grid\GridView;
	use yii\helpers\Html;

	/* @var $this yii\web\View */
	/* @var $searchModel app\models\FgInvoiceDetailSearch */
	/* @var $dataProvider yii\data\ActiveDataProvider */
	$this->title = Yii::t('app', 'Fg invoice details');
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fg-invoice-detail-index">

	<h1><?=Html::encode($this->title)?></h1>
	<?=$this->render('__details', [
		'dataProvider' => $dataProvider,
		'searchModel' => $searchModel,
	])?>
	<p>
		<?=Html::a(Yii::t('app', 'Create Fg invoice detail'), ['create'], ['class' => 'btn btn-success'])?>
	</p>


</div>
