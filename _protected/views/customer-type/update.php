<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\CustomerType */
	$this->title = Yii::t('app', 'Update', [
		'name' => $model->name,
	]);
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Customer types'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="customer-type-update">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
