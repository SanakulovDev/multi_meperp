<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\PartOrderDetail */
	$this->title = Yii::t('app', 'Update Part Order Detail: {name}', ['name' => $model->part->part_no,]);
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Part order details'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="part-order-detail-update">
	<?=$this->render('_form', ['model' => $model,])?>
</div>
