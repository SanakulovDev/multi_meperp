<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\Customer */
	$this->title = Yii::t('app', 'Add a new customer');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Customer info'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-create">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
