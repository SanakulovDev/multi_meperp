<?php
	use yii\helpers\Html;
	use yii\web\YiiAsset;
	use yii\widgets\DetailView;

	/* @var $this yii\web\View */
	/* @var $model app\models\InvoicePartProblem */
	$this->title = $model->id;
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Invoice Part Problems'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
	YiiAsset::register($this);
?>
<div class="invoice-part-problem-view">

	<h1><?=Html::encode($this->title)?></h1>

	<p>
		<?=Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary'])?>
		<?=Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
				'method' => 'post',
			],
		])?>
	</p>

	<?=DetailView::widget([
		                      'model' => $model,
		                      'attributes' => [
			                      'id',
			                      'inv_detail_id',
			                      'part_order_no',
			                      'contract_no',
			                      'created_by',
			                      'created_at',
			                      'updated_by',
			                      'updated_at',
		                      ],
	                      ])?>

</div>
