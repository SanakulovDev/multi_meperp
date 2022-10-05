<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\ContractSubject */
	$this->title = Yii::t('app', 'Update');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Consolidation type'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="consolidation-type-update">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
