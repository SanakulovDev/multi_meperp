<?php
use app\models\PartOrder;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\InvoiceDetail */
/* @var $form yii\widgets\ActiveForm */
/* @var TYPE_NAME $containerInvoice */
/* @var TYPE_NAME $partNo */
/* @var TYPE_NAME $contractList */
?>

  <div class="invoice-detail-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
      <div class="col-lg-5">
        <div class="form-group field-invoicedetail-remarks has-success">
          <label class="control-label" for="invoicedetail-remarks"><?=Yii::t('app', 'Container Invoices')?></label>
          <input type="text" class="form-control" name="partNo" value="<?=$containerInvoice?>" disabled="true">
        </div>
      </div>
      <div class="col-lg-4">
        <div class="form-group field-invoicedetail-remarks has-success">
          <label class="control-label" for="invoicedetail-remarks"><?=Yii::t('app', 'Part No')?></label>
          <input type="text" class="form-control" name="partNo" value="<?=$partNo?>" disabled="true">
        </div>
      </div>
      <div class="col-lg-3">
        <?=$form->field($model, 'qty')->textInput(['type' => 'text', 'disabled' => true])?>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-4">
        <?
        $params = ['prompt' => '. . .', null, 'class' => 'form-control select2'];
        echo $form->field($model, 'contract_id')->dropDownList($contractList, $params);
        ?>
      </div>
      <div class="col-lg-4">
        <? $partOrders = PartOrder::find()->select(['id', 'order_no', 'iss_dt'])
                                  ->orderBy(['order_no' => SORT_DESC, 'iss_dt' => SORT_DESC])
                                  ->all();
        $items = ArrayHelper::map($partOrders, 'id', 'order_no');
        $params = ['prompt' => '. . .', null, 'class' => 'form-control select2'];
        echo $form->field($model, 'part_order_id')->dropDownList($items, $params);
        ?>
        <? //=$form->field($model, 'part_order_id')->dropdownlist([], ['prompt' => '. . .', null, 'class' => 'form-control select2']);?>
      </div>
      <div class="col-lg-4">
        <?=$form->field($model, 'price')->textInput(['type' => 'number', 'step' => '0.00001', 'maxlength' => true])?>
      </div>
    </div>

    <div class="row ">
      <div class="col-lg-12">
        <?=$form->field($model, 'remarks')->textInput(['maxlength' => true])?>
      </div>
    </div>

    <input type="hidden" name="<?=Yii::$app->request->csrfParam;?>" value="<?=Yii::$app->request->getCsrfToken();?>"/>
    <div class="form-group pull-right">
      <?=Html::a(Yii::t('app', 'btn-cancel'), ['container-invoice/view', 'id' => $model->cont_inv_id], ['class' => 'btn btn-default btn-sm'])?>
      <?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
    </div>

    <?php ActiveForm::end(); ?>

  </div>

<?
$urlOrder = Url::to(['invoice-detail/order-list-by-contract-and-part'], true);
$script1 = <<< JS
$(document).ready(function() {
	
	var contract_id = $('#invoicedetail-contract_id').children("option:selected").val();
	if(contract_id>0){
		let url = '$urlOrder' + '?id=' + contract_id+'&partId=$model->part_id';
	$.get(url, function(data, status){
	  // clear
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
	}
	
	$(document).on("select2:select", "#invoicedetail-contract_id", function(e) {
	 let data = e.params.data;
	 let url = '$urlOrder'+'?id='+data.id+'&partId=$model->part_id';
	 $.get(url, function(data, status){
	  // clear
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
	
});
JS;
$this->registerJs($script1);
?>
