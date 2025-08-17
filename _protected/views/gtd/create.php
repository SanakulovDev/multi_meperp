<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\Gtd */
	/** @var TYPE_NAME $errorlist */
	$this->title = Yii::t('app', 'Create');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Customs declaration'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gtd-create">
	<?=$this->render('_form', [
		'errorlist' => $errorlist ?? null,
		'model' => $model,
	])?>
</div>
