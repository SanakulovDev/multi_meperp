<?php
	$this->title = Yii::t('app', 'Create');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Points'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ship-mode-create">

	<?=$this->render('_form', [
		'model' => $model,
		'shipModes' => $shipModes,
	])?>

</div>
