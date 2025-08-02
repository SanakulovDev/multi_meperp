<?php
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\FgInvoiceDetail */
	/* @var $form yii\widgets\ActiveForm */
	/** @var TYPE_NAME $part_items */
	/** @var TYPE_NAME $pt_order_id */
?>
	<style>
		.w_40{width:40%;}
	</style>
	<div class="part-order-detail-form">
		<?php $form = ActiveForm::begin(); ?>
		<fieldset class="scheduler-border">
			<legend class="scheduler-border">
				<span id="addItem" class="btn btn-success btn-sm" title="<?=Yii::t('app', 'Add detail')?>">+</span>
				<input id="selected_part_ids" type="hidden" value="0"/>
			</legend>
			<table class="table table-striped table-bordered table-condensed table-sm-padding_2_0">
				<thead>
				<tr>
					<th class="text-center"><i class="fa fa-fw fa-gears"></i></th>
					<th class="w_40"><?=Yii::t('app', 'Part')?></th>
					<th><?=Yii::t('app', 'Qty')?></th>
					<th><?=Yii::t('app', 'Unit')?></th>
					<th><?=Yii::t('app', 'Price')?></th>
					<th><?=Yii::t('app', 'Amount')?></th>
				</tr>
				</thead>
				<tbody id="pt_order_detail"></tbody>
			</table>
		</fieldset>
		<div class="form-group pull-right">
			<?=Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
			<?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
		</div>
		<?php ActiveForm::end(); ?>
	</div>

<?php
	$add_item = <<< JS
$(document).ready(function() {
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
	$(document).on('change', '.item_qty', function(){
	  let tr_id = $(this).closest('tr').attr('id');
	  let pt_summa = $("#qty"+tr_id).val() * $("#price"+tr_id).val();
	  $("#pt_summa"+tr_id).text(pt_summa);
	  $("#summa"+tr_id).val(pt_summa);
	});
	$(document).on('click', '#addItem', function(){
	 var cur_time = new Date().getTime();
		 var append_row ="<tr id='"+cur_time+"' class='v_urta'>"+
			"<td class='text-center'><i class='row_remove fa fa-remove font-weight-bold text-danger'></i></td>"+
				"<td><select id='mySelect2"+cur_time+"' class='w_40 s2_part_no' name='items[part_no][]'></select></td>"+
				"<td><input type='text' class='form-control item_qty' id='qty"+cur_time+"' name='items[qty][]'/></td>"+
				"<td><span id='txt_unit"+cur_time+"'></span></td>"+
				"<td><span id='txt_price"+cur_time+"'></span><input type='hidden' class='form-control' id='price"+cur_time+"' name='items[price][]'/></td>"+
				"<td><span id='pt_summa"+cur_time+"'></span><input type='hidden' class='form-control' id='summa"+cur_time+"' name='items[summa][]'/></td>"+
			"</tr>";
		 $("#pt_order_detail").append(append_row);
		 $('.s2_part_no').select2({
		  ajax: {
				url: '/part-order/part-list',
				data: function (params) {
					return query = {
						q:params.term,
						contract:$("#cont_id").val(), 
						part_ids: $('#selected_part_ids').val(),
						model_id: $model->id
					}
				}
			},
			templateResult: formatRepo,
      templateSelection: formatRepoSelection
			}).on('change', function (){
				var tr_id = $(this).closest('tr').attr('id');
				var contract_id = $('#cont_id').val();
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
					url: "/part-order/part-data",
					type: "post",
					data: {
						contractid: contract_id,
						partid: this.value,
					},
					success: function(response) {
						$("#txt_unit"+tr_id).text(response.unit);
						$("#txt_price"+tr_id).text(response.price);
						$("#price"+tr_id).val(response.price);
						let pt_summa = $("#qty"+tr_id).val() * response.price * $("#fginvoice-summa").val() / 100;
						$("#pt_summa"+tr_id).text(pt_summa);
						$("#summa"+tr_id).val(pt_summa);
					},
					error: function(xhr) {
						console.log(xhr);
					}
				});
			});
		  function formatRepo (repo) {
		  if (repo.loading) {
		    return repo.text;
		  }
		  var text = $( "<div class='select2-result-repository clearfix'>"  + repo.text + repo.days_count + "</div>" );			
			  // text.find(".select2-result-repository__title").text(repo.text);			
			  return text;
			}
		  function formatRepoSelection (repo) {
			  return repo.text;
			}
	});
});
JS;
	$this->registerJs($add_item, yii\web\View::POS_END);
?>