<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\ProductLine */
	$this->title = Yii::t('app', 'Add');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'The production lines'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-line-create">
	<?=$this->render('_form', [
		'model' => $model,
	])?>
</div>
