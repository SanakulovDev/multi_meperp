<?php
	use yii\helpers\Html;

	/* @var $this yii\web\View */
	/* @var $model app\models\InvoicePartProblem */
	$this->title = Yii::t('app', 'Update Invoice Part Problem: {name}', [
		'name' => $model->id,
	]);
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Invoice Part Problems'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="invoice-part-problem-update">

	<h1><?=Html::encode($this->title)?></h1>

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
