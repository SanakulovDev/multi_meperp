<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\DocumentType */
	$this->title = Yii::t('app', 'Create document type');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Document types'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-type-create">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
