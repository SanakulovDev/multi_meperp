<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\ContractSubject */
	$this->title = Yii::t('app', 'Create');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Consolidation type'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="consolidation-type-create">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
