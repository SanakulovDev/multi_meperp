<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\PaymentType */
	$this->title = Yii::t('app', 'Create');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Payment types'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-type-create">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
