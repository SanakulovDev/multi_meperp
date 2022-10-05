<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\Truck */
	$this->title = Yii::t('app', 'Create Truck');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Trucks'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="truck-create">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
