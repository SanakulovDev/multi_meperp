<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\PaymentTerm */
	$this->title = Yii::t('app', 'Update');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Payment terms'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="payment-term-update">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
