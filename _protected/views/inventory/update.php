<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\Api */
	$this->title = $model->invinfo;
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Physical Inventory'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="api-update">

	<?=$this->render('_form', [
		'model' => $model,
		'searchModel' => $searchModel,
		'dataProvider' => $dataProvider,
		'parts' => $parts,
		'uoms' => $uoms,
	])?>

</div>
