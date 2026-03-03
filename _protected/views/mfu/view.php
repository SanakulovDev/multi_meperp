<?php
	use yii\helpers\Html;
	use yii\web\YiiAsset;
	use yii\widgets\DetailView;

	/* @var $this yii\web\View */
	/* @var $model app\models\Mfu */
	$this->title = $model->part->partinfo;
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'MFU'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
	YiiAsset::register($this);
?>
<div class="mfu-view">

	<p>
		<?=Html::a(Yii::t('app', 'btn-save'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm'])?>
		<?=
			Html::a(Yii::t('app', 'btn-delete'), ['delete', 'id' => $model->id], [
				'class' => 'btn btn-danger btn-sm',
				'data' => [
					'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
					'method' => 'post',
				],
			])
		?>
	</p>

	<?=
		DetailView::widget([
			                   'model' => $model,
			                   'template' => '<tr><th style="width:250px">{label}</th><td>{value}</td></tr>',
			                   'attributes' => [
				                   'id',
				                   [
					                   'attribute' => 'part_id',
					                   'value' => $model->part->partinfo,
				                   ],
				                   'average',
				                   'capacity',
				                   [
					                   'attribute' => 'ship_mode_id',
					                   'value' => $model->shipMode->name ?? null,
				                   ],
				                   'mfu_code',
				                   [
					                   'attribute' => 'contract_source_id',
					                   'value' => $model->contractSource->name ?? null,
				                   ],
				                   'bank',
				                   [
					                   'attribute' => 'constraint',
					                   'value' => ($model->constraint == 1) ? Yii::t('app', 'YES') : Yii::t('app', 'NO'),
				                   ],
				                   [
					                   'attribute' => 'consolidation_type_id',
					                   'value' => $model->consolidationType->name ?? null,
				                   ],
				                   [
					                   'attribute' => 'moq',
					                   'value' => $model->moq ?? null,
				                   ],
				                   [
					                   'attribute' => 'created_by',
					                   'value' => $model->createdBy->fullname ?? null,
				                   ],
				                   [
					                   'attribute' => 'created_at',
					                   'value' => $model->createdAtFormatted ?? null,
				                   ],
				                   [
					                   'attribute' => 'updated_by',
					                   'value' => $model->updatedBy->fullname ?? null,
				                   ],
				                   [
					                   'attribute' => 'updated_at',
					                   'value' => $model->updatedAtFormatted ?? null,
				                   ],
			                   ],
		                   ])
	?>

</div>
