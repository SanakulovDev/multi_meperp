<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\ApiDetail */
	$this->title = Yii::t('app', 'Create inventorization detail');
	$this->params['breadcrumbs'][] = ['label' => $model->api->invinfo, 'url' => ['inventory/update', 'id' => $model->api_id]];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="api-detail-create">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
