<?php
use app\models\ContainerInvoice;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\InvoiceDetail */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="invoice-detail-form">

  <?php $form = ActiveForm::begin(); ?>
  <div class="row">
    <div class="col-lg-12">
      <?
      if ($model->isNewRecord) {
        $disable_enable_container_invoice = false;
      } else {
        $disable_enable_container_invoice = true;
      }
      $data = ContainerInvoice::find()
                              ->select(['container_invoice.id as id', 'concat(invoice.invoice_no,"(",container.container_no,")-",shipped_at) as container_no'])
                              ->leftJoin('container', 'container.id=container_invoice.container_id')
                              ->leftJoin('invoice', 'invoice.id=container_invoice.invoice_id')
                              ->all();
      $items = ArrayHelper::map($data, 'id', 'container_no');
      $params = ['disabled' => $disable_enable_container_invoice, 'prompt' => '. . .', null, 'class' => 'form-control select2'];
      echo $form->field($model, 'cont_inv_id')->dropDownList($items, $params);
      ?>
    </div>
  </div>
  <?=$this->render(
    '_form2',
    [
      'model' => $model,
      'form' => $form,
    ]
  );?>
  <input type="hidden" name="<?=Yii::$app->request->csrfParam;?>" value="<?=Yii::$app->request->getCsrfToken();?>"/>
  <div class="form-group">
    <!-- <?=Html::a(Yii::t('app', 'btn-cancel'), ['container-invoice/view', 'id' => $model->cont_inv_id], ['class' => 'btn btn-default btn-sm'])?> -->
    <button type="button" id="delete<?php echo($index) ?>" class="btn btn-danger btn-sm">Удалить</button>
    <?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
  </div>

  <?php ActiveForm::end(); ?>

</div>

<?
$urlOrder = Url::to(['invoice-detail/order-list-by-contract'], true);
$urlPart = Url::to(['invoice-detail/part-list-by-order'], true);
$script1 = <<< JS
$(document).ready(function() {
	var contract_id = $('#invoicedetail-contract_id').children("option:selected"). val();
	let url = '$urlOrder' + '?id=' + contract_id;
	$.get(url, function(data, status){
	  // clear
	  $('#invoicedetail-part_order_id').find('option').remove();
	  $('#invoicedetail-part_id').find('option').remove();
	  // append
	  $.each(data, function () {
	    $('#invoicedetail-part_order_id').append(new Option(this.text, this.id));
	  });
	  var data = {
      "id": $("#invoicedetail-part_order_id option:first").val(),
      "text": $("#invoicedetail-part_order_id option:first").text()
    };    
    $('#invoicedetail-part_order_id').trigger({
        type: 'select2:select',
        params: {
            data: data
        }
    });
	});
	
	$(document).on("select2:select", "#invoicedetail-contract_id", function(e) {
	 let data = e.params.data;
	 let url = '$urlOrder'+'?id='+data.id;
	 $.get(url, function(data, status){
	  // clear
	  $('#invoicedetail-part_id').find('option').remove();
	  $('#invoicedetail-part_order_id').find('option').remove();
	  // append
	  $.each(data, function () {
	    $('#invoicedetail-part_order_id').append(new Option(this.text, this.id));
	  });	  
	  var data = {
      "id": $("#invoicedetail-part_order_id option:first").val(),
      "text": $("#invoicedetail-part_order_id option:first").text()
    };    
    $('#invoicedetail-part_order_id').trigger({
        type: 'select2:select',
        params: {
            data: data
        }
    });
	 });
	});
	
	$(document).on("select2:select", "#invoicedetail-part_order_id", function(e) {	  
	 let data = e.params.data;
	 let url = '$urlPart'+'?id='+data.id;
	 $.get(url, function(data, status){
	  // clear
	  $('#invoicedetail-part_id').find('option').remove();
	  // append	  
	  $.each(data, function () {
	    $('#invoicedetail-part_id').append(new Option(this.text, this.id));
	  });
	  
	 });
	});
});
JS;
$this->registerJs($script1);
?>
