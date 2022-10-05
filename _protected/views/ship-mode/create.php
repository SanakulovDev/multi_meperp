<?php
	$this->title = Yii::t('app', 'Create track type');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ship mode'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ship-mode-create">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
