<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\ReceivingPerson */
	$this->title = Yii::t('app', 'Create');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Attorney letter'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="receiving-person-create">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
