<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\Invoice */
	$this->title = Yii::t('app', 'Create invoice');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Invoices'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-create">
	<?=$this->render('_form', [
		'errorlist' => ($errorlist ?? null),
		'model' => ($model ?? null),
		'items' => ($items ?? null),
		'modelItems' => ($modelItems ?? null),
	])?>

</div>
