<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\PaymentType */
	$this->title = Yii::t('app', 'Update');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Payment types'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="payment-type-update">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
