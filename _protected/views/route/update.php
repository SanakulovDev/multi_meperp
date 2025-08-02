<?php
	$this->title = Yii::t('app', 'Update');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Routes'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="ship-mode-update">

	<?=$this->render('_form', [
		'model' => $model,
		'shipModes' => $shipModes,
		'points' => $points,
	])?>

</div>
