<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\Part */
	$this->title = Yii::t('app', 'Update part: {nameAttribute}', [
		'nameAttribute' => $model->part_no,
	]);
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Parts'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="part-update">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
