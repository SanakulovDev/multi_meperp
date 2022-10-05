<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\ContractDetail */
	$this->title = Yii::t('app', 'Create');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'FG contract'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contract-detail-create">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
