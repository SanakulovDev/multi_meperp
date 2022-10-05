<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\ReceivingPerson */
	$this->title = Yii::t('app', 'Update').' '.$model->doc_date.' '.$model->doc_number;
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Attorney letter'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $model->doc_date.' '.$model->doc_number, 'url' => ['view', 'id' => $model->id]];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="receiving-person-update">

	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
