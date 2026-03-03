<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\DeliveryTerm */
	$this->title = Yii::t('app', 'Create');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Delivery terms'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="delivery-term-create">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
