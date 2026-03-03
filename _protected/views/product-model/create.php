<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\ProductModel */
	$this->title = Yii::t('app', 'Add');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'OEM models'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-model-create">
	<?=$this->render('_form', [
		'model' => $model,
	])?>
</div>
