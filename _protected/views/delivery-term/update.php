<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\DeliveryTerm */
	$this->title = Yii::t('app', 'Update');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Delivery terms'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="delivery-term-update">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
