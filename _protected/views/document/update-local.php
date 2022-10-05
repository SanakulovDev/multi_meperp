<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\Document */
	$this->title = Yii::t('app', 'Update').' : '.$model->docnum;
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Document'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $model->docnum, 'url' => ['view', 'id' => $model->id]];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="document-update">

	<?=$this->render('_form-local', [
		'errorlist' => ($errorlist ?? null),
		'model' => ($model ?? null),
		'items' => ($items ?? null),
		'modelItems' => ($modelItems ?? null),
		'user_warehouses' => ($user_warehouses ?? null),
	])?>

</div>
