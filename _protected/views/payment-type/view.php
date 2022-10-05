<?php
	use yii\helpers\Html;
	use yii\web\YiiAsset;
	use yii\widgets\DetailView;

	/* @var $this yii\web\View */
	/* @var $model app\models\PaymentType */
	$this->title = $model->title;
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Payment types'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
	YiiAsset::register($this);
?>
<div class="payment-type-view">

	<p>
		<?=Html::a(Yii::t('app', 'btn-update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary'])?>
		<?=Html::a(Yii::t('app', 'btn-delete'), ['delete', 'id' => $model->id], [
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
			                      'title',
			                      'description',
			                      [
				                      'attribute' => 'created_by',
				                      'value' => $model->createdBy->fullname
			                      ],
			                      [
				                      'attribute' => 'created_at',
				                      'value' => $model->createdAtFormatted
			                      ],
			                      [
				                      'attribute' => 'updated_by',
				                      'value' => $model->updatedBy->fullname
			                      ],
			                      [
				                      'attribute' => 'updated_at',
				                      'value' => $model->updatedAtFormatted
			                      ],
		                      ],
	                      ])?>

</div>
