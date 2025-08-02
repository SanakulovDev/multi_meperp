<?php
	use kartik\datetime\DateTimePicker;
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\Gtd */
	/* @var $form yii\widgets\ActiveForm */
	/* @var TYPE_NAME $errorlist */
?>

<div class="gtd-form">

	<?php $form = ActiveForm::begin(); ?>

	<div class="row">
		<div class="col-md-3 col-sm-3 col-lg-3">
			<?=$form->field($model, 'gtd_no')->textInput(['maxlength' => true, 'class' => 'form-control input-sm'])?>
		</div>
		<div class="col-md-3 col-sm-3 col-lg-3">
			<?=$form->field($model, 'gtd_dt')->widget(DateTimePicker::classname(), [
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
					'class' => 'form-control input-sm'
				]
			]);
			?>
		</div>
		<div class="col-md-3 col-sm-3 col-lg-3">
			<?=$form->field($model, 'post_no')->textInput(['maxlength' => true, 'class' => 'form-control input-sm'])?>
		</div>
		<div class="col-md-3 col-sm-3 col-lg-3">
			<!--			<span id="gtd_amount">0000000</span>-->
		</div>
	</div>
	<?
		//		echo "<pre>1:"; print_r($errorlist);echo "</pre>";
		//		die;
		if(isset($errorlist)){
			if(isset($errorlist) && strlen(trim($errorlist)) > 1){
				?>
				<div class='alert alert-danger'>
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span></button>
					<strong><?=Yii::t('app', 'Error').'!!!<br>'?></strong> <?=$errorlist;?>
				</div>
				<?
			}
		}
	?>
	<fieldset class="scheduler-border">
		<legend class="scheduler-border">
			<span id="addItem" class="btn btn-success btn-sm" title="<?=Yii::t('app', 'Add detail')?>">+</span>
			<input id="selected_invoice_ids" type="hidden" value="0"/>
		</legend>
		<table class="table table-striped table-bordered table-condensed table-sm-padding_2_0">
			<thead>
			<tr>
				<th class="text-center"><i class="fa fa-fw fa-gears"></i></th>
				<th><?=Yii::t('app', 'Invoice')?></th>
				<th><?=Yii::t('app', 'Supplier')?></th>
				<th><?=Yii::t('app', 'Amount')?></th>
			</tr>
			</thead>
			<tbody id="inv_detail"></tbody>
		</table>
	</fieldset>
	<hr>
	<div class="form-group pull-right">
		<?=Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
		<?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-success btn-sm'])?>
	</div>
	<?php ActiveForm::end(); ?>
</div>

<?
	$add_item = <<< JS
	$(document).ready(function() {	  
	  
		$(document).on('click', '.row_remove', function(){
			 	var tr_id = $(this).closest('tr').attr('id');
			 	let this_inv_id = $("#mySelect2"+tr_id).val();
			 	// alert(this_inv_id);
			 	var new_val = "";
			 	let selected_invoice_ids = $("#selected_invoice_ids");
			 	new_val = selected_invoice_ids.val().replace(','+this_inv_id, '');
				selected_invoice_ids.val(new_val);
			 	new_val = selected_invoice_ids.val().replace(this_inv_id+',', '');
				selected_invoice_ids.val(new_val);
			 	$(this).closest('tr').remove();			 
		});
		
		$(document).on('click', '#addItem', function(){	
			 var cur_time = new Date().getTime();				 
			 var append_row ="<tr id='"+cur_time+"' class='v_urta'>"+
				"<td class='text-center'><i class='row_remove fa fa-remove font-weight-bold text-danger'></i></td>"+
					"<td><select id='mySelect2"+cur_time+"' class='s2_inv_no' name=\"items[inv_no][]\"></select></td>"+				
					"<td><span id='txt_supplier"+cur_time+"'></span></td>"+	
					"<td><input type='text' class='form-control item_amount' id='amount"+cur_time+"' name=\"items[amount][]\"/></td>"+																
				"</tr>";
			 $("#inv_detail").append(append_row);
			 $('.s2_inv_no').select2({
			 ajax: {
					url: 'invoice-list',
					data: function (params) {
						return query = { q:params.term, invoive_ids: $('#selected_invoice_ids').val()}
					}
				},
				
			}).on('change', function (){
					var tr_id = $(this).closest('tr').attr('id');
					var selected_invoice_ids = $('#selected_invoice_ids').val() + ','+ this.value;						
					arr=selected_invoice_ids.split(",");						
					newArr=[];						
					for(var i=0;i<arr.length;i++){						
						isIn=0;						
						for(var j=0;j<newArr.length;j++){						
							if(arr[i]==newArr[j]){
								isIn=1;
							}						
						}						
						if(isIn==0){
							newArr.push(arr[i])
						}						
					}
					$('#selected_invoice_ids').val(newArr);
					$.ajax({
						url: "invoice-data",
						type: "post",
						data: { 
							invoice_id: this.value,
						},
						success: function(response) {
							$("#txt_supplier"+tr_id).text(response.supplier);
						},
						error: function(xhr) {
							console.log(xhr)
						}
					});
			});
		});
	
	});
JS;
	$this->registerJs($add_item, yii\web\View::POS_END);
?>

