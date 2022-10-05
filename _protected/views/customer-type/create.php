<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\CustomerType */
	$this->title = Yii::t('app', 'Add new customer type');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Customer types'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-type-create">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
