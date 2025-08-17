<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\Crushing */
	$this->title = Yii::t('app', 'Create');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Shredding'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="crushing-create">

	<?=$this->render('_form', [
		'model' => $model,
		'parts' => $parts
	])?>

</div>
