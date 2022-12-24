<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\Contract */
	$this->title = Yii::t('app', 'Create');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales contracts'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contract-create">

	<?=$this->render('_form', [
		'errorlist' => ($errorlist ?? null),
		'model' => ($model ?? null),
		'items' => ($items ?? null),
		'modelItems' => ($modelItems ?? null),
		'isNewRecord' => ($isNewRecord ?? null),
		'update' => false
	])?>

</div>
