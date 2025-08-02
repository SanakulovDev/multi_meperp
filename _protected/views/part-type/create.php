<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\PartType */
	$this->title = Yii::t('app', 'Add');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'The types of part'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-type-create">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
