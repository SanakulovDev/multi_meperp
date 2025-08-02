<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\ContractDetail */
	$this->title = Yii::t('app', 'Update');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'FG contract'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="contract-detail-update">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
