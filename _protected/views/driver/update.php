<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\Driver */
	$this->title = Yii::t('app', 'Update Driver').': '.$model->first_name." ".$model->last_name." ".$model->middle_name;
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Drivers'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="driver-update">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
