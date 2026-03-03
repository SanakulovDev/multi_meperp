<?php
	$this->title = Yii::t('app', 'Update track type').Yii::t('app', ': {nameAttribute}', [
			'nameAttribute' => $model->name,
		]);
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ship mode'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="ship-mode-update">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
