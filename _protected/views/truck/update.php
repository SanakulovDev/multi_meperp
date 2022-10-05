<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\Truck */
	$this->title = Yii::t('app', 'Update сar').': <strong>'.$model->model.'</strong>('.$model->number.')';
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Trucks'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="truck-update">
	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
