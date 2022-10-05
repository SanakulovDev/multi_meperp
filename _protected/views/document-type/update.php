<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\DocumentType */
	$this->title = Yii::t('app', 'Update document type: {nameAttribute}', [
		'nameAttribute' => $model->name,
	]);
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Document types'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="document-type-update">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
