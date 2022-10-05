<?php
	use yii\helpers\Html;

	/* @var $this yii\web\View */
	/* @var $model app\models\Uloc */
	$this->title = Yii::t('app', 'Create Uloc');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ulocs'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="uloc-create">

	<h1><?=Html::encode($this->title)?></h1>

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
