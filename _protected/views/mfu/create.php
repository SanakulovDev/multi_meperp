<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\Mfu */
	$this->title = Yii::t('app', 'Create');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'MFU'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mfu-create">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
