<?php
/* @var $this yii\web\View */
/* @var $model app\models\InvoiceDetail */
$this->title = Yii::t('app', 'Create invoice detail');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'invoice details'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="invoice-detail-create">
  <div>
      <?=$this->render('_invoice-form', [
        'model' => $invoice_data,
      ])?>
			<?=$this->render('_form', [
        'model' => $model,
        'id' => $invoice_data->id
      ])?>
	</div>
</div>
