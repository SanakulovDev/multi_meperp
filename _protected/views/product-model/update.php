<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\ProductModel */
	$this->title = Yii::t('app', 'Update', [
		'name' => $model->id,
	]);
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'OEM models'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="product-model-update">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
