<?
	use app\components\Helpers;
	use app\controllers\PartController;
	use app\models\Part;
	use yii\helpers\Html;
	use yii\helpers\Url;

	$cond = ['status' => 1];
	if(!isset($isLocalKd)){
		$isLocalKd = false;
	}
	if(!isset($isRecDaval)){
		$isRecDaval = false;
	}
	$part_list = [];
	if(!$isRecDaval){
        if (!$isLocalKd) {
            foreach (Part::find()->where($cond)->all() as $part) {
               $part_list[$part->id] = $part->partinfo;
            }
        }else{
			// Update actionda tanlangan Supplier bo'yicha detallar spiskasini olish
			if(!$model->isNewRecord){
				$part_list = yii\helpers\ArrayHelper::map(PartController::getPartsBySupplier($model->supplier_id), 'id', 'info');
			}
		}
	}else{
		// Update actionda tanlangan Supplier WH bo'yicha detallar spiskasini olish
		if(!$model->isNewRecord){
			$part_list = yii\helpers\ArrayHelper::map(PartController::getPartsByFloc($model->from_warehouse_id), 'id', 'info');
		}
	}
	$total = 0;
	
?>

<?= $this->render('../common/_loading'); ?>

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<div class="row">
					<div class="col-md-6"><?=Yii::t('app', 'Details')?></div>
					<div class="col-md-4">
						<input type="file" class="form-control pull-right" name="file" id="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, application/wps-office.xls">
					</div>
					<div class="col-md-1">
						<span class="btn btn-sm btn-primary pull-left" id="but_upload">
							<?=Yii::t('app','Upload')?>
						</span>
					</div>
					<div class="col-md-1">
							<button type="button" class="btn btn-success btn-sm pull-right btnAddDetail " title="<?=Yii::t('app', 'Add new detail (F2)')?>">
								<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
							</button>
					</div>
				</div>
      </div>
			<? if(is_array($errorlist) and count($errorlist) > 0){ ?>
				<div class="alert alert-danger alert-dismissible">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<h4><i class="icon fa fa-ban"></i> <?=Yii::t('app', 'Correct the following errors.')?></h4>
					<?
						if(is_array($errorlist['details']) and count($errorlist['details']) > 0){
							foreach($errorlist['details'] as $key => $errList){
								if(!in_array($key, ['no_item'])){
									echo '<b>'.$key.' - строка :</b><br/>';
								}
								foreach($errList as $err){
									foreach($err as $e){
										echo ' - '.$e.'<br/>';
									}
								}
								echo "<br/>";
							}
						}
					?>

					<?
						if(is_array($errorlist['stock'] ?? null) and count($errorlist['stock'] ?? null) > 0){
							echo '<b>'.Yii::t('app', 'No enough stock!').'</b><br/>';
							foreach($errorlist['stock'] as $key => $err){
								echo ' - '.$err.'<br/>';
							}
						}
					?>
					<?
						if(isset($errorlist['stock_receipt'])){
							if(is_array($errorlist['stock_receipt']) and count($errorlist['stock_receipt']) > 0){
								echo '<b>'.Yii::t('app', 'Stock receipt errors!').'</b><br/>';
								foreach($errorlist['stock_receipt'] as $key => $err){
									echo ' - '.$err.'<br/>';
								}
							}
						}
					?>
					<?
						if(isset($errorlist['stock_issue'])){
							if(is_array($errorlist['stock_issue']) and count($errorlist['stock_issue']) > 0){
								echo '<b>'.Yii::t('app', 'Stock issue errors!').'</b><br/>';
								foreach($errorlist['stock_issue'] as $key => $err){
									echo ' - '.$err.'<br/>';
								}
							}
						}
					?>
				</div>
			<? } ?>

			<table class="table" id="detailTable">


				<tr>
					<th style="width: 20px">№</th>
					<th style="width: 250px"><?=Yii::t('app', 'Detail')?></th>
					<th class="partname"><?=Yii::t('app', 'Part name')?></th>
					<th class="unit" style="width: 100px"><?=Yii::t('app', 'Unit')?></th>
					<th class="stock" style="width: 100px"><?=Yii::t('app', 'Stock')?></th>
					<th style="width: 100px"><?=Yii::t('app', 'Quantity')?></th>
					<th style="width: 20px"><?=Yii::t('app', 'Action')?></th>
				</tr>

				<tr id="tr_template" style="display: none">
					<?=Html::input('hidden', 'items[id][]', '', ['class' => 'form-control']);?>
					<th scope="row" style="text-align:  center;vertical-align:  middle;">
						<input type="hidden" name="items[num][]" value=""></th>
					<td><?=Html::dropDownList('items[detail][]', null, $part_list, ['id' => 'partlist', 'class' => 'form-control detail_part', 'prompt' => Yii::t('app', 'Select...'), 'data-url' => Url::toRoute(['part/get-partname'])]);?></td>
					<td class="partname"></td>
					<td class="unit"></td>
					<td class="stock"></td>
					<td><?=Html::input('text', 'items[quantity][]', '', ['class' => 'form-control detail-qty']);?></td>
					<td style="text-align: center;vertical-align: middle">
						<span class="glyphicon glyphicon-trash text-danger removeIcon" aria-hidden="true"></span>
					</td>
				</tr>


				<?
					if(isset($items['detail'])){
						if(is_array($items['detail']) and count($items['detail']) > 1){
							foreach($items['detail'] as $key => $value){
								if($key == 0){
									continue;
								}
								?>
								<tr class="tr_item">
									<?=Html::input('hidden', 'items[id][]', $items['id'][$key] ?? null, ['class' => 'form-control']);?>
									<th scope="row" style="text-align:  center;vertical-align:  middle;">
										<input type="hidden" name="items[num][]" value="<?=$key?>"><?=$key?></th>
									<td><?=Html::dropDownList('items[detail][]', $items['detail'][$key], $part_list, ['class' => 'form-control detail_part', 'prompt' => Yii::t('app', 'Select...'), 'data-url' => Url::toRoute(['part/get-partname'])]);?></td>
									<td class="partname"><?=$items['partname'][$key] ?? null?></td>
									<td class="unit"><?=$items['unit'][$key] ?? null?></td>
									<td class="stock"><?=Helpers::formatRemoveDecimal(($items['stock'][$key] ?? null))?></td>
									<td><?=Html::input('text', 'items[quantity][]', Helpers::formatRemoveDecimal($items['quantity'][$key]), ['class' => 'form-control detail-qty']);?></td>
									<td style="text-align: center;vertical-align: middle">
										<span class="glyphicon glyphicon-trash text-danger removeIcon" aria-hidden="true"></span>
									</td>
								</tr>
								<?
								if(!empty($items['quantity'][$key])){
									$total += $items['quantity'][$key];
								}
							}
						}
					}
				?>

			</table>
			<div class="panel-footer">
				<p>
					&nbsp;
					<button type="button" class="btn btn-success btn-sm pull-right btnAddDetailFooter" title="<?=Yii::t('app', 'Add new detail (F2)')?>">
						<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
					</button>

					</button>
					<span class="pull-right" style="margin-right: 55px;margin-top: 5px;"><?=Yii::t('app', 'Total')?>: <b id="total"><?=Helpers::formatRemoveDecimal($total)?></b></span>
				</p>
			</div>
		</div>
	</div>
</div>

<?php

	$uploadUrl = Url::toRoute('/document/upload');

	$script1 = <<< JS

	

	$(".detail_part").trigger('change');  
	$(".detail_part:not(#tr_template td .detail_part)").select2();
	$("#but_upload").click(function() {

			$('#loading').show();

			var fd = new FormData();
			var files = $('#file')[0].files[0];
			fd.append('file',files);
			
			$.ajax({
					url: '$uploadUrl',
					type: 'post',
					data: fd,
					contentType: false,
					processData: false,
					success: function(response){
						$.each(response, function( index, value ) {
							$.when($('.btnAddDetail').trigger('click')).done(function(){
								$('#detailTable tr:last select.detail_part').val(index).trigger('change');
								$('#detailTable tr:last input.detail-qty').val(value);
							}); 
						});

						$('#loading').hide();

					},
			});
	});  

	$('#loading').hide();

JS;
	$this->registerJs($script1);
?>
