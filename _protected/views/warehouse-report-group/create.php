<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\WarehouseReportGroup */
	$this->title = Yii::t('app', 'Create');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Warehouse report groups'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<?
	if(isset($errorlist) && count($errorlist)){
		echo '<div class="alert-danger alert fade in">';
		echo '<strong>'.Yii::t('app', 'Error').'</strong>';
		foreach($errorlist as $err_index => $err_value){
			echo "<p><strong>".$err_index.":</strong> ".$err_value.'</p>';
		}
		echo '</div>';
	}
?>
<div class="warehouse-report-group-create">
	<?=$this->render('_form', [
		'model' => $model,
	])?>

</div>
