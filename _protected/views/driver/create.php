<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\Driver */
	$this->title = Yii::t('app', 'Create Driver');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Drivers'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="driver-create">
	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
