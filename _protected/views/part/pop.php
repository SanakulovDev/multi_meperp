<?php
	use app\components\Helpers;
	use yii\helpers\ArrayHelper;
	use yii\widgets\ActiveForm;

	/* @var $this yii\web\View */
	/* @var $model app\models\Part */
	$this->title = Yii::t('app', 'POP');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Parts'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>
<style>
	.pop_data{
		display:inline-block;
		border:1px solid;
		padding:2px 3px;
		margin-bottom:3px;
	}
	.pop_data_red{
		border-color:red;
	}
	.pop_data_gray{
		border-color:#ddd;
	}
	.pop-table td{
		padding:1px !important;
	}
	.pop-title{
		margin:5px !important;
	}
	.pop-panel-body{
		padding:5px !important;
	}
	.pop-panel-body-top{
		font-size:12px !important;
	}
	.qty-zero{
		color:gray;
	}
	.qty-bold{
		font-weight:bold;
	}
	.qty-danger{
		color:#a94442;
		font-weight:bold;
	}
</style>
<div class="part-create">

	<div class="panel panel-default">
		<div class="panel-body pop-panel-body-top">
			<div class="row">
				<div class="col-sm-9 invoice-col">
					<div class="row">
						<div class="col-sm-3 invoice-col">
							<?php $form = ActiveForm::begin(['id' => 'form_read']); ?>
							<?=
								$form->field($model, 'part_id')->dropDownList(ArrayHelper::map(app\models\Part::find()->where(['state' => app\models\Part::STATE_RAW])->all(), 'id', 'partinfo'), [
									'class' => ' form-control select2 input-sm',
									'prompt' => Yii::t('app', 'Select'),
									'onchange' => 'this.form.submit()'
								])->label('PART');
							?>
							<?php ActiveForm::end(); ?>

							<span class="pop_data pop_data_gray"><b>WBAL :</b> <?=Helpers::formatRemoveDecimal($data['req']->whbal ?? null) ?? '-'?></span><br>
							<span class="pop_data pop_data_gray"><b>LBAL :</b> <?=Helpers::formatRemoveDecimal($data['req']->linebal ?? null) ?? '-'?></span><br>
							<span class="pop_data pop_data_gray"><b>SEMI :</b> <?=Helpers::formatRemoveDecimal($data['req']->semistock ?? null) ?? '-'?></span><br>
							<span class="pop_data pop_data_gray"><b>PEND :</b> <?=Helpers::formatRemoveDecimal($data['req']->pending ?? null) ?? '-'?></span><br>
							<span class="pop_data pop_data_gray"><b>SBAL :</b> <?=Helpers::formatRemoveDecimal($data['req']->outsourcing ?? null) ?? '-'?></span><br>
							<span class="pop_data pop_data_gray"><b>TBAL :</b> <?=Helpers::formatRemoveDecimal($data['req']->arrive ?? null) ?? '-'?></span><br>
							<span class="pop_data pop_data_gray"><b>TOTAL :</b> <b><?=Helpers::formatRemoveDecimal($data['req']->totalstock ?? null) ?? '-'?></b></span><br>
							<span class="pop_data pop_data_gray"><b>API :</b> <?=Helpers::formatRemoveDecimal($data['api']['inventory_qty'] ?? null) ?? '-'?></span><br>

						</div>
						<div class="col-sm-3">
							<span class="pop_data pop_data_gray"><b>PART NAME :</b> <?=$data['part']->part_name ?? '-'?></span><br>
							<span class="pop_data pop_data_gray"><b>UOM :</b> <?=$data['part']->unit->unit_value ?? '-'?></span><br>
							<span class="pop_data pop_data_red"><b>YTD RCV :</b> 65500</span><br>
							<span class="pop_data pop_data_red"><b>YTD ISS :</b> 58400</span><br>
							<br>
							<span class="pop_data pop_data_red"><b>LAST RECV. DT :</b> 12.06.2019</span><br>
							<span class="pop_data pop_data_red"><b>LAST RECV. QTY :</b> 2500</span><br>
							<br>
							<span class="pop_data pop_data_red"><b>LAST ISS. DT :</b> 13.06.2019</span><br>
							<span class="pop_data pop_data_red"><b>LASR ISS. QTY :</b> 3540</span><br>
						</div>
						<div class="col-sm-3 invoice-col">
							<span class="pop_data pop_data_gray"><b>PART TYPE :</b> <?=$data['part']->partType->typename ?? '-'?></span><br>
							<span class="pop_data pop_data_gray"><b>PROD TYPE :</b> <?=$data['part']->stateText ?? '-'?></span><br>
							<span class="pop_data pop_data_red"><b>DLOC :</b> WH2</span><br>
							<span class="pop_data pop_data_gray"><b>ULOC :</b> <?=$data['part']->bom->warehouse->name ?? '-'?></span><br>
							<span class="pop_data pop_data_gray"><b>USAGE QTY :</b> <?=Helpers::formatRemoveDecimal($data['part']->bom->usage_qty ?? null) ?? '-'?></span><br>
							<br>
							<span class="pop_data pop_data_gray"><b>SUPP ID :</b> <?=$data['part']->contractDetail->contract->supplier->duns ?? '-'?></span><br>
							<span class="pop_data pop_data_gray"><b>SUPP NAME :</b> <?=$data['part']->contractDetail->contract->supplier->name ?? '-'?></span><br>
							<span class="pop_data pop_data_gray"><b>CONT. SOURCE :</b> <?=$data['part']->contractSource->name ?? '-'?></span><br>
							<span class="pop_data pop_data_gray"><b>PRICE :</b> <?=Helpers::formatRemoveDecimal($data['part']->actualContract->price ?? null) ?? '-'?> <?=$data['part']->actualContract->contract->currency->code ?? '-'?></span><br>
						</div>
						<div class="col-sm-3 invoice-col">
							<span class="pop_data pop_data_gray"><b>MODEL :</b> <?=$data['part']->productModel->modelname ?? '-'?></span><br>
							<span class="pop_data pop_data_gray"><b>P.PART NO :</b> <?=$data['part']->bom->part->part_no ?? '-'?></span><br>
							<span class="pop_data pop_data_gray"><b>P.PART NAME :</b> <?=$data['part']->bom->part->part_name ?? '-'?></span><br>
							<span class="pop_data pop_data_red"><b>MRP :</b> UD2145</span><br>
							<span class="pop_data pop_data_red"><b>WH :</b> -</span><br>
							<span class="pop_data pop_data_red"><b>O/S :</b> <span class="text-danger">2350</span></span><br>
							<span class="pop_data pop_data_gray"><b>DOH :</b> <span <? if(isset($data['doh']) and $data['doh'] < 10) echo 'class="text-danger"' ?>><?=$data['doh'] ?? '-'?></span></span><br>
							<span class="pop_data pop_data_gray"><b>PCS TO GO :</b> <span><?=$data['pcstogo'] ?? '-'?></span></span><br>
							<span class="pop_data pop_data_gray"><b>COM. USER:</b> <?=$data['part']->commentedBy->fullname ?? '-'?></span><br>
							<span class="pop_data pop_data_gray"><b>COM. DATE:</b> <?=$data['part']->commentedAtFormatted ?? '-'?></span><br>
						</div>
					</div>


					<? if(isset($data['part']->id)){ ?>
						<div class="row" style="margin-top: 10px;margin-bottom: 10px;">
							<div class="col-sm-12">
								<?php $form = ActiveForm::begin(['id' => 'form_write']); ?>
								<div class="input-group input-group-sm">
									<input type="text" class="form-control" name="comment" placeholder="Comment..." value="<?=$data['part']->comment ?? null?>">
									<input type="hidden" name="hidden_part_id" value="<?=$data['part']->id ?? '-'?>">
									<span class="input-group-btn">
                                <button type="submit" name="submitComment" class="btn btn-success btn-flat">Save</button>
                            </span>
								</div>
								<?php ActiveForm::end(); ?>
							</div>
						</div>
					<? } ?>


					<div class="row">
						<div class="col-sm-12 invoice-col">
							<div class="panel panel-default">
								<div class="panel-body pop-panel-body">
									<h4 class="pop-title">INTRANSIT</h4>
									<table class="table table-hover pop-table">
										<tbody>
										<tr>
											<th>#</th>
											<th style="text-align: right;padding-right: 5px;">QTY</th>
											<th>Container</th>
											<th>Invoice NO</th>
											<th>Curr. Loc</th>
											<th>Curr. Date</th>
											<th>ETA</th>
										</tr>
										<?
											$i = 0;
											if(is_array($data['intransit']) and count($data['intransit']) > 0){
												foreach($data['intransit'] as $intrans){
													?>

													<tr>
														<td><?=++$i?></td>
														<td style="text-align: right;">
															<span style="padding-right: 10px;"><?=Helpers::formatRemoveDecimal($intrans['qty'])?></span></td>
														<td><?=$intrans['container_no']?></td>
														<td><?=$intrans['invoice_no']?></td>
														<td><?=$intrans['curr_loc']?></td>
														<td><?=$intrans['curr_date']?></td>
														<td><?=$intrans['estdate']?></td>
													</tr>
													<?
												}
											}else{
												echo '<tr><td>'.Yii::t('app', 'Data not found').'<td><tr>';
											}
										?>


										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-3 invoice-col">
					<div class="panel panel-default">
						<div class="panel-body pop-panel-body">
							<h4 class="pop-title">COVERAGE</h4>
							<table class="table table-hover pop-table">
								<tbody>
								<tr>
									<th>#</th>
									<th>Date</th>
									<th style="text-align: right">Req.</th>
									<th style="text-align: right">Intrans.</th>
									<th style="text-align: right">End/Stock</th>
								</tr>
								<?
									$i = 0;
									if(is_array($data['coverage']) and count($data['coverage']) > 0){
										foreach($data['coverage'] as $cover){
											?>

											<tr>
												<td><?=++$i?></td>
												<td><?=$cover['date']?></td>
												<td style="text-align: right"
													<?
														if($cover['req'] < 0)
															echo 'class="qty-danger"';
														elseif($cover['req'] == 0)
															echo 'class="qty-zero"';
														else
															echo 'class="qty-bold"';
													?>>
													<?=Helpers::formatRemoveDecimal($cover['req'])?>
												</td>
												<td style="text-align: right"
													<?
														if($cover['intrans'] < 0)
															echo 'class="qty-danger"';
														elseif($cover['intrans'] == 0)
															echo 'class="qty-zero"';
														else
															echo 'class="qty-bold"';
													?>>
													<?=Helpers::formatRemoveDecimal($cover['intrans'])?>
												</td>
												<td style="text-align: right"
													<?
														if($cover['stock'] < 0)
															echo 'class="qty-danger"';
														elseif($cover['stock'] == 0)
															echo 'class="qty-zero"';
														else
															echo 'class="qty-bold"';
													?>>
													<?=Helpers::formatRemoveDecimal($cover['stock'])?>
												</td>
											</tr>
											<?
										}
									}else{
										echo '<tr><td>'.Yii::t('app', 'Data not found').'<td><tr>';
									}
								?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>


		</div>
	</div>

</div>
