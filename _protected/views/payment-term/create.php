<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\PaymentTerm */
	$this->title = Yii::t('app', 'Create');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Payment terms'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-term-create">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
