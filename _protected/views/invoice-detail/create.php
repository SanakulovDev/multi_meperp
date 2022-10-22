<?php
/* @var $this yii\web\View */
/* @var $model app\models\InvoiceDetail */
$this->title = Yii::t('app', 'Create invoice detail');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'invoice details'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="invoice-detail-create">
  <div>
    <?php if(count($status) !== 1) { ?>
      <?=$this->render('_invoice-form', [
        'model' => $invoice_data,
      ])?>
    <?php } ?>
		<?php foreach ($status as $count_of_component): ?>
			<?=$this->render('_form', [
        'model' => $model,
        'id' => $invoice_data->id,
        'index' => $count_of_component
      ])?>
		<?php endforeach; ?>
    <button type="button" <?php echo ($invoice_data->id); ?> onclick="onSave()" class="btn btn-success btn-sm">Сохранить</button>
    <?php print_r($invoice_data->id) ?>
	</div>
</div>

<?php
	$aaaa = <<< JS
	function onSave (id) {
    let aaaa = $invoice_data->id
    console.log(id);
    console.log('id', aaaa)
		const count = $status;
		for (let i = 1; i <= count; i++) {
			$('form#w' + i).submit();
		}
		if (aaaa) {
			window.location.href = "/container-invoice/view?id=" + aaaa;
		}
	}
JS;
	$this->registerJs($aaaa, yii\web\View::POS_END);
?>
