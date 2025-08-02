<?php
	use yii\helpers\Html;
	use yii\web\YiiAsset;
	use yii\widgets\DetailView;

	/* @var $this yii\web\View */
	/* @var $model app\models\ReceivingPerson */
	$this->title = $model->doc_number.' '.$model->doc_date;
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Attorney letter'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
	YiiAsset::register($this);
?>
<div class="receiving-person-view">

	<p>
		<?=Html::a(Yii::t('app', 'btn-update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm'])?>
		<?=Html::a(Yii::t('app', 'btn-delete'), ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger btn-sm',
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
			                      'fullname',
			                      'doc_number',
			                      'doc_date',
			                      [
				                      'label' => Yii::t('app', 'Status'),
				                      'attribute' => 'status',
				                      'format' => 'html',
				                      'value' => function($model, $column){
					                      $sts_value = $model->status;
					                      switch($sts_value){
						                      case 1:
							                      $class = 'success';
							                      $sts_name = "✔";
							                      $sts_title = Yii::t('app', 'Active');
							                      break;
						                      case 0:
							                      $class = 'danger';
							                      $sts_name = "✖";
							                      $sts_title = Yii::t('app', 'Inactive');
							                      break;
					                      }
					                      $html = Html::tag('span', Html::encode($sts_name), ['class' => 'label label-'.$class]);
					                      return $sts_value === null ? $column->grid->emptyCell : $html;
				                      },
			                      ],
			                      [
				                      'attribute' => 'created_at',
				                      'value' => $model->createdAtFormatted,
			                      ],
			                      [
				                      'attribute' => 'created_by',
				                      'value' => $model->createdBy->fullname,
			                      ],
			                      [
				                      'attribute' => 'updated_at',
				                      'value' => $model->updatedAtFormatted,
			                      ],
			                      [
				                      'attribute' => 'updated_by',
				                      'value' => $model->updatedBy->fullname,
			                      ],
		                      ],
	                      ])?>

</div>
