<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\ContractSource */
	$this->title = Yii::t('app', 'Update');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Contract sources'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="contract-source-update">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
