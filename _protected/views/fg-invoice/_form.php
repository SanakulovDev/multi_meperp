<?php
use app\models\Customer;
use app\models\Factory;
use kartik\datetime\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\FgInvoice */
/* @var $form yii\widgets\ActiveForm */
/* @var TYPE_NAME $errorlist */
?>
<div class="fg-invoice-form">
  <?php $form = ActiveForm::begin(); ?>
  <div class="row">
    <div class="col-md-3 col-sm-3 col-lg-3">
      <?=$form->field($model, 'invoice_no')
              ->label(Yii::t('app', 'FG Invoice no (TTN)'))
              ->textInput(['maxlength' => true, 'class' => 'form-control input-sm'])
      ?>
    </div>
    <div class="col-md-3 col-sm-3 col-lg-3">
      <?=$form->field($model, 'invoice_date')
              ->label(Yii::t('app', 'Waybill date'))
              ->widget(DateTimePicker::classname(), [
                'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
                'layout' => '{picker}{input}{remove}',
                'removeButton' => ['position' => 'append'],
                'language' => 'ru',
                'pluginOptions' => [
                  'autoclose' => true,
                  'format' => 'yyyy-mm-dd',
                  'startView' => 'month',
                  'minView' => 'month',
                  'maxView' => 'month',
                ],
                'options' => [
                  'autocomplete' => 'off',
                  'placeholder' => 'YYYY-MM-DD',
                  'class' => 'form-control'
                ]
              ]);
      ?>
    </div>
    <div class="col-md-3 col-sm-3 col-lg-3">
      <?
      $factories = Factory::find()->all();
      $factory_items = ArrayHelper::map($factories, 'id', 'factoryinfo');
      $model->factory_id = array_key_first($factory_items);
      $params = ['prompt' => '. . .', 'class' => 'form-control'];
      echo $form->field($model, 'factory_id')->dropDownList($factory_items, $params);
      ?>
    </div>
    <div class="col-md-3 col-sm-3 col-lg-3">
      <?
      $customers = Customer::find()->all();
      $customer_items = ArrayHelper::map($customers, 'id', 'name');
      $params = ['prompt' => '. . .', 'class' => 'form-control input-sm select2'];
      echo $form->field($model, 'customer_id')->dropDownList($customer_items, $params);
      ?>
    </div>
  </div>
  <div class="row">
    <div class="col-md-3 col-sm-3 col-lg-3">
      <?
      echo $form->field($model, 'contract')->dropDownList([]);
      ?>
    </div>
    <div class="col-md-2 col-sm-2 col-lg-2">
      <?=$form->field($model, 'vat')->textInput(['value' => Yii::$app->params['vat'], 'maxlength' => true, 'class' => 'form-control'])?>
    </div>
    <!--
    <div class="col-md-2 col-sm-2 col-lg-2">
			<? /*
				$doveronnosts = ReceivingPerson::find()->select(['fullname', 'doc_number'])->all();
				$doveronnost_items = ArrayHelper::map($doveronnosts, 'fullname', 'doc_number');
			*/ ?>
			<datalist id="doveronnost_list">
				<? /*
					foreach($doveronnost_items as $key => $item){
						*/ ?>
						<option value="<? /*=$key*/ ?>"><? /*=$item*/ ?></option>
					<? /* }
				*/ ?>
			</datalist>
			<? /* echo $form->field($model, 'rec_person_fullname')->textInput(['id' => 'rec_person_fullname', 'list' => "doveronnost_list", 'maxlength' => true, 'class' => 'form-control input-sm']) */ ?>
		</div>

		<div class="col-md-3 col-sm-3 col-lg-3">
			<? /* echo $form->field($model, 'rec_person_regno')->textInput(['id' => 'rec_person_regno', 'maxlength' => true, 'class' => 'form-control input-sm']) */ ?>
		</div>

		<div class="col-md-2 col-sm-2 col-lg-2">
			<?php
    /*				$drivers = Driver::find()->select(["id", "CONCAT(first_name, ' ',last_name,' ',middle_name,'(',emp_no,')' ) as first_name"])->all();
            $driver_items = ArrayHelper::map($drivers, 'first_name', 'first_name');
            $params = ['prompt' => '. . .', 'class' => 'form-control input-sm'];
          */ ?>
			<datalist id="driver_list">
				<? /*=Html::dropDownList('driver_list', null, $driver_items, $params)*/ ?>
			</datalist>
            <? /*=$form->field($model, 'driver')->textInput(['list' => "driver_list", 'maxlength' => true, 'class' => 'form-control input-sm'])*/ ?>
		</div>

		<div class="col-md-2 col-sm-2 col-lg-2">
			<?php
    /*				$trucks = Truck::find()->select(["id", "CONCAT(model, '(', number,')') as model"])->all();
            $truck_items = ArrayHelper::map($trucks, 'model', 'model');
            $params = ['prompt' => '. . .', 'class' => 'form-control input-sm'];
                  */ ?>

			<datalist id="truck_list">
				<? /*=Html::dropDownList('truck_list', null, $truck_items, $params)*/ ?>
			</datalist>

            <? /*=$form->field($model, 'truck')->textInput(['list' => "truck_list", 'maxlength' => true, 'class' => 'form-control input-sm']) */ ?>
		</div>
    -->
  </div>
  <!--<div class="row">
		<div class="col-md-2 col-sm-2 col-lg-2">
			<? /*=$form->field($model, 'sender')->textInput(['maxlength' => true, 'class' => 'form-control input-sm'])*/ ?>
		</div>
		<div class="col-md-2 col-sm-2 col-lg-2">
			<? /*=$form->field($model, 'vat')->textInput(['value' => Yii::$app->params['vat'], 'maxlength' => true, 'class' => 'form-control input-sm'])*/ ?>
		</div>
		<div class="col-md-2 col-sm-2 col-lg-2">
			<? /*=$form->field($model, 'excise')->textInput(['value' => Yii::$app->params['excise'], 'maxlength' => true, 'class' => 'form-control input-sm'])*/ ?>
		</div>
	</div>-->
  <div class="row">
    <div class="col-sm-12">
      <?=$form->field($model, 'comment')->textInput(['class' => 'form-control input-sm'])?>
    </div>
  </div>
  <?
  if(isset($errorlist) && count($errorlist)) {
    echo '<div class="alert-danger alert fade in">';
    echo '<strong>'.Yii::t('app', 'Error').'</strong>';
    echo "<pre>Error:";
    print_r($errorlist);
    echo "</pre>";
    echo '</div>';
  }
  ?>
  <fieldset class="scheduler-border">
    <legend class="scheduler-border">
      <span id="addItem" class="btn btn-success btn-sm" title="<?=Yii::t('app', 'Add detail')?>">+</span>
      <input id="selected_part_ids" type="hidden" value="0" />
    </legend>
    <table class="table table-striped table-bordered table-condensed table-sm-padding_2_0">
      <thead>
      <tr>
        <th class="text-center"><i class="fa fa-fw fa-gears"></i></th>
        <th><?=Yii::t('app', 'PartName')?></th>
        <th><?=Yii::t('app', 'Qty')?></th>
        <th><?=Yii::t('app', 'Unit')?></th>
        <th><?=Yii::t('app', 'Price')?></th>
        <th><?=Yii::t('app', 'Vat')?></th>
        <!--				<th>--><? //=Yii::t('app', 'Excise')?><!--</th>-->
      </tr>
      </thead>
      <tbody id="fg_inv_detail"></tbody>
    </table>
  </fieldset>
  <hr>
  <div class="form-group pull-right">
    <?=Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
    <?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
  </div>
  <?php ActiveForm::end(); ?>
</div>

<?php
$url = Url::to(['sales-contract/list-by-sales-supplier'], true);
$add_item = <<< JS
$(document).ready(function() {
	$('#fginvoice-contract').select();
	var customer_id = $('#fginvoice-customer_id').children("option:selected").val();
	let url = '$url' + '?id=' + customer_id;
	$.get(url, function(data, status){
	  // clear
	  $('#fginvoice-contract').find('option').remove();
	  // append
	  $.each(data, function () {
	    $('#fginvoice-contract').append(new Option(this.text, this.id));
	  });
	});
	$(document).on("select2:select", "#fginvoice-customer_id", function(e) {
	 $("#fg_inv_detail").text('');
	 let data = e.params.data;
	 let url = '$url'+'?id='+data.id;
	 $.get(url, function(data, status){
	  // clear
	  $('#fginvoice-contract').find('option').remove();
	  // append
	  $.each(data, function () {
	    $('#fginvoice-contract').append(new Option(this.text, this.id));
	  });
	 });
	});
	$(document).on('change', '#rec_person_fullname', function(){
	$("#rec_person_regno").val("");
	  var optionslist = $('#doveronnost_list')[0].options;
	  var value = $(this).val();
	  for(var x=0; x<optionslist.length; x++){
	   if(optionslist[x].value === value) {
	     $("#rec_person_regno").val(optionslist[x].text);
	    break;
	   }
	  }
	});
	$(document).on('click', '.row_remove', function(){
	  var tr_id = $(this).closest('tr').attr('id');
	  let this_inv_id = $("#mySelect2"+tr_id).val();
	  let new_val = '';
	  let selected_part_ids = $("#selected_part_ids");
	  new_val = selected_part_ids.val().replace(','+this_inv_id, '');
	  selected_part_ids.val(new_val);
	  new_val = selected_part_ids.val().replace(this_inv_id+',', '');
	  selected_part_ids.val(new_val);
	  $(this).closest('tr').remove();
	});
	$(document).on('change', '#fginvoice-contract', function(){
	   $("#fg_inv_detail").text('');
	});
	$(document).on('change', '.item_qty', function(){
	  let tr_id = $(this).closest('tr').attr('id');
	  let new_vat = $("#qty"+tr_id).val() * $("#price"+tr_id).val() * $("#fginvoice-vat").val() / 100;
	  // let new_excise = $("#qty"+tr_id).val() * $("#price"+tr_id).val() * $("#fginvoice-excise").val() / 100;
	  $("#txt_vat"+tr_id).text(new_vat);
	  $("#vat"+tr_id).val(new_vat);
	  // $("#txt_excise"+tr_id).text(new_excise);
	  // $("#excise"+tr_id).val(new_excise);
	});
	$(document).on('click', '#addItem', function(){
	 var cur_time = new Date().getTime();
		 var append_row ="<tr id='"+cur_time+"' class='v_urta'>"+
			"<td class='text-center'><i class='row_remove fa fa-remove font-weight-bold text-danger'></i></td>"+
				"<td><select id='mySelect2"+cur_time+"' class='s2_part_no' name=\"items[part_no][]\"></select></td>"+
				"<td><input type='text' class='form-control item_qty' id='qty"+cur_time+"' name=\"items[qty][]\"/></td>"+
				"<td><span id='txt_unit"+cur_time+"'></span></td>"+
				"<td><span id='txt_price"+cur_time+"'></span><input type='hidden' class='form-control' id='price"+cur_time+"' name=\"items[price][]\"/></td>"+
				"<td><span id='txt_vat"+cur_time+"'></span><input type='hidden' class='form-control' id='vat"+cur_time+"' name=\"items[vat][]\"/></td>"+
				// "<td><span id='txt_excise"+cur_time+"'></span><input type='hidden' class='form-control' id='excise"+cur_time+"' name=\"items[excise][]\"/></td>"+
			"</tr>";
		 $("#fg_inv_detail").append(append_row);
		 $('.s2_part_no').select2({
		 ajax: {
				url: 'part-list',
				data: function (params) {
					return query = {
						q:params.term,
						id:null,
						fg_contract:$("#fginvoice-contract").val(), 
						part_ids: $('#selected_part_ids').val()
					}
				}
			},
		}).on('change', function (){
				var tr_id = $(this).closest('tr').attr('id');
				var fg_contract_id = $('#fginvoice-contract').val();
				var selected_part_ids = $('#selected_part_ids').val() + ','+ this.value;
				arr=selected_part_ids.split(",");
				newArr=[];
				for(var i=0;i<arr.length;i++){
					isIn=0;
					for(var j=0;j<newArr.length;j++){
						if(arr[i]==newArr[j]){
							isIn=1;
						}
					}
					if(isIn==0){
						newArr.push(arr[i]);
					}
				}
				$('#selected_part_ids').val(newArr);
				$.ajax({
					url: "part-data",
					type: "post",
					data: {
						fg_contractid: fg_contract_id,
						partid: this.value,
					},
					success: function(response) {
						$("#txt_unit"+tr_id).text(response.unit);
						$("#txt_price"+tr_id).text(response.price);
						$("#price"+tr_id).val(response.price);
						let new_vat = $("#qty"+tr_id).val() * response.price * $("#fginvoice-vat").val() / 100;
						// let new_excise = $("#qty"+tr_id).val() * response.price * $("#fginvoice-excise").val() / 100;
						$("#txt_vat"+tr_id).text(new_vat);
						$("#vat"+tr_id).val(new_vat);
						// $("#txt_excise"+tr_id).text(new_excise);
						// $("#excise"+tr_id).val(new_excise);
					},
					error: function(xhr) {
						console.log(xhr);
					}
				});
		});
	});
});
JS;
$this->registerJs($add_item, yii\web\View::POS_END);
?>
