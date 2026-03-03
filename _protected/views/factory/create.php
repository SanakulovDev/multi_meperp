<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\Factory */
	$this->title = Yii::t('app', 'Create');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Factories'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="factory-create">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
