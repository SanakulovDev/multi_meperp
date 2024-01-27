<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\Document */

use yii\helpers\Url;

$this->title = Yii::t('app', 'Create document');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Document'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-create-info">

	<?=$this->render('_form-info', [
		'errorlist' => ($errorlist ?? null),
		'model' => $model,
		'items' => ($items ?? null),
		'modelItems' => ($modelItems ?? null),
		'isNewRecord' => ($isNewRecord ?? null),
		'user_warehouses' => ($user_warehouses ?? null),
	])?>

</div>