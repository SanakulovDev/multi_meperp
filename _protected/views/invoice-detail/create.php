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
    <div style="text-align: right; padding: 7px;">
      <button type="button" id="save-all" data-count="<?php echo count($status); ?>" onclick="onSave()" class="btn btn-success btn-sm">Далее</button>
    </div>
	</div>
</div>

<?php
	$aaaa = <<< JS
	function onSave (id) {
    let aaaa = $invoice_data->id
    let count = $('#save-all').data('count')
		// for (let i = 1; i <= count; i++) {
    //   $('form#w' + i).submit();
		// }
    setTimeout(() => {
        if (!($("div").find('[aria-invalid=true]').length)) {
          window.location.href = "/container-invoice/view?id=" + aaaa;
        }
    }, 2000);
	}
JS;
	$this->registerJs($aaaa, yii\web\View::POS_END);
?>
