<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\ProductionOrder */
	$this->title = $model->serialNumber;
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Production order'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="production-order-update">

	<?=$this->render('_form', [
		'model' => $model,
		'parts' => $parts
	])?>

</div>
