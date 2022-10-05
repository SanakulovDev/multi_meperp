<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\Document */
	$this->title = Yii::t('app', 'Create document');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Document'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-create">

	<?=$this->render('_form-local', [
		'errorlist' => $errorlist ?? null,
		'model' => $model,
		'items' => $items,
		'modelItems' => $modelItems,
		'isNewRecord' => $isNewRecord,
		'user_warehouses' => $user_warehouses,
	])?>

</div>
