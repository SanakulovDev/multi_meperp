<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\Contract */
	$this->title = Yii::t('app', 'Update');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Contract Supplier'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="contract-update">

	<?=$this->render('_form', [
		'errorlist' => ($errorlist ?? null),
		'model' => ($model ?? null),
		'items' => ($items ?? null),
		'modelItems' => ($modelItems ?? null)
	])?>

	<div>
		<?php foreach ($count as $count_of_component): ?>
			<?=$this->render('_form_detail', [
				'model_detail' => $model_detail,
				'id' => $model->id,
				'index' => $count_of_component
			])?>
		<?php endforeach; ?>
	</div>


</div>
