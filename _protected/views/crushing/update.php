<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\Crushing */
	$this->title = Yii::t('app', 'Update Crushing: {name}', [
		'name' => $model->id,
	]);
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Shredding'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="crushing-update">

	<?=$this->render('_form', [
		'model' => $model,
		'parts' => $parts
	])?>

</div>
