<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\PartType */
	$this->title = Yii::t('app', 'Update');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'The types of product'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="product-type-update">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
