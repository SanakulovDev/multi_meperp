<?php
/* @var $this yii\web\View */
use kartik\select2\Select2;
use yii\helpers\Url;
use app\enums\FreightInvoiceType;

/**
 * @var $model            app\models\FreightInvoiceDetail
 * @var $form             yii\widgets\ActiveForm
 * @var $containers       app\controllers\FreightInvoiceDetailController
 * @var $invoices         app\controllers\FreightInvoiceDetailController
 * @var $selectedInvoices app\controllers\FreightInvoiceDetailController
 */
?>
<input type="hidden" name="containers" id="containers" value='<?=json_encode($containers)?>'>
<input type="hidden" name="out-containers" id="out-containers" value='<?=json_encode($outContainers)?>'>
<input type="hidden" name="out-invoices" id="out-invoices" value='<?=json_encode($outInvoices)?>'>
<? if($freightInvoice->isInbound){?>
	<div class="row">
		<div class="col-lg-3">
			<?=$form->field($model, 'isNeededOutbound')->checkbox(['id' => 'is-needed-outbound'])->label('');?>
		</div>
		<div class="col-lg-6">
		<label class="form-group has-float-label">
			<?=$form->field($model, 'outInvoice')->widget(Select2::classname(), [
				'data' => $outInvoices,
				'options' => ['prompt' => '. . .', 'id'=>'out-invoice', 'class' => 'form-control'],
				'pluginOptions' => ['allowClear' => true],
			]);?>
		</label>
		</div>
	</div>
<?}?>

<div class="row">
	<div class="col-sm-3">
		<label class="form-group has-float-label">
    <?=$form->field($model, 'container_id')->widget(Select2::classname(), [
      'data' => $containers,
      'options' => ['prompt' => '. . .', 'id'=>'container-id', 'class' => 'form-control'],
      'pluginOptions' => ['allowClear' => true],
    ]);?>
		</label>
	</div>
	<div class="col-sm-9">
		<label class="form-group has-float-label">
    <?=Select2::widget([
      'name' => 'childInvoice',
      'value' => $selectedInvoices,
      'data' => $invoices,
      'maintainOrder' => true,
      'options' => [ 'multiple' => true, 'placeholder' => '...',  'id'=>'child-invoice', 'class' => 'form-control'],
      'pluginOptions' => ['maximumInputLength' => 10],
    ])?>
			<span><?=Yii::t('app', 'Invoices')?></span>
		</label>
	</div>
</div>
<label class="form-group has-float-label">
<?=$form->field($model, 'comment')->textarea(['rows' => 1])?>
</label>

<?php
$url = Url::to(['freight-invoice-detail/invoice-list-by-container'], true);
$urlGetOutContainers = Url::to(['freight-invoice-detail/out-containers-list-by-out-invoice'], true);
$container_id = $model->container_id;
$add_item = <<< JS
$(document).ready(function() {

	$(document).on("select2:select", "#container-id", function(e) {
	 $("#child-invoice").val('');
	 let data = e.params.data;
	 let url = '$url'+'?id='+data.id;
	 console.log(url);
	 $.get(url, function(data){
	  // clear
	  $('#child-invoice').find('option').remove();
	  // append
	  $.each(data, function () {
	    $('#child-invoice').append(new Option(this.text, this.id));
	  });
	 });
	});


	$(document).on("change", "#is-needed-outbound", function(e) {
	let checked = $(this).is(":checked");
	if(checked){
		
		// Show Outbound freight invoices select box
		$('.field-out-invoice').show();

		// Clear container select list
		$('#container-id').find('option').remove();

		// Clear invoice select list
		$('#child-invoice').find('option').remove();
		
		// Fill container list with Out containers
		fillContainerListWithOut($('#out-invoice').val());

 	}else{

			// Hide Outbound freight invoice select box
			$('.field-out-invoice').hide();

			// Fill container select box with common containers
			fillContainerList();
			
			// Clear invoice select list
			$('#child-invoice').find('option').remove()

	 }

	});

	function fillContainerList(){
			// clear
			$('#container-id').find('option').remove();
			let data = JSON. parse($('#containers').val());

			// append
			$('#container-id').append(new Option('...', ''));
			$.each(data, function (key, value) {
				$('#container-id').append(new Option(value, key));
			});
	}
	
	// - Getting out containers list by out invoce
	// - Fill container select box 
	$(document).on("select2:select", "#out-invoice", function(e) {

		// Clear invoice select list
		$('#child-invoice').find('option').remove();
		
		fillContainerListWithOut(e.params.data.id);

	});

	function fillContainerListWithOut(id){
		let container_id = $('#container-id').val();
		$("#container-id").val('');
		let url = '$urlGetOutContainers' + '?id=' + id;

		$.get(url, function(data){
	  // clear
	  $('#container-id').find('option').remove();
		// append
		$('#container-id').append(new Option('...', ''));
	  $.each(data, function () {
	    $('#container-id').append(new Option(this.text, this.id));
	  });
		$('#container-id').val(container_id);
	 });

	}

	if($('#is-needed-outbound').is(":checked")){
		
		fillContainerListWithOut($('#out-invoice').val());
		

	}else{
		$('.field-out-invoice').hide();
	}
	

});
JS;
$this->registerJs($add_item, yii\web\View::POS_END);
?>
