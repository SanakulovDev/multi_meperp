<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\ContractSource */
	$this->title = Yii::t('app', 'Create');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Contract sources'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contract-source-create">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
