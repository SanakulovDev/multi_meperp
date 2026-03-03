<?php
	/* @var $this yii\web\View */
	/* @var $model app\models\FgInvoiceDetail */
	/** @var TYPE_NAME $inv_name_dt */
	/** @var TYPE_NAME $part_items */
	/** @var TYPE_NAME $part_params */
	/** @var TYPE_NAME $fg_inv_id */
	$this->title = Yii::t('app', 'Create Fg invoice detail for {name}', ['name' => $inv_name_dt]);
	$this->title = Yii::t('app', 'Add Fg invoice detail(TTN) for {name}', ['name' => $inv_name_dt]);
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Fg invoice details'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fg-invoice-detail-create">
	<?=

		$this->render('_create_form', [
			'model' => $model,
			'inv_name_dt' => $inv_name_dt,
			'part_items' => $part_items,
			'fg_inv_id' => $fg_inv_id,
		])?>

</div>
