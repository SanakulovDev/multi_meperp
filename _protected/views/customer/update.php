<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\Customer */
	$this->title = Yii::t('app', 'Update', [
		'name' => $model->name,
	]);
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Customer info'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="customer-update">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
