<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\Defect */
	$this->title = Yii::t('app', 'Create Defect');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Defects'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="defect-create">
	<?=$this->render('_form', [
		'model' => $model,
	])?>
</div>
