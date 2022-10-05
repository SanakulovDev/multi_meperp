<?php
	use yii\helpers\Html;

	/* @var $this yii\web\View */
	/* @var $model app\models\PartPart */
	$this->title = Yii::t('app', 'Create Part Part');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'BOM'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;

	/** @var TYPE_NAME $parentParts */
	/** @var TYPE_NAME $notRawParts */
	/** @var TYPE_NAME $notFgParts */
	/** @var TYPE_NAME $parts */
	/** @var TYPE_NAME $warehouses */
?>
<div class="part-part-create">

	<h1><?=Html::encode($this->title)?></h1>

	<?=$this->render('_form', [
		'model' => $model,
		'parentParts' => $parentParts,
		'notRawParts' => $notRawParts,
		'notFgParts' => $notFgParts,
		'parts' => $parts,
		'warehouses' => $warehouses,
	])?>

</div>
