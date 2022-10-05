<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\Mfu */
	$this->title = Yii::t('app', 'Update');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'MFU'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="mfu-update">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
