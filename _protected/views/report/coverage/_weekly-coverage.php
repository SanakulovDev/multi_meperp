<?

use app\components\Helpers;
use app\models\Part;

?>

<table class="table table-req" id="fix_table_w">

	<thead>
		<tr class="tr_minus_count">
			<td colspan="17"></td>
			<td colspan="100" class="wd_title" style="text-align: left;"><?= Yii::t('app', 'No of short items') ?></td>
		</tr>

		<tr class="tr_minus_count">
			<td colspan="7">

				<div class="form-inline wf-div-filter" style="display: none;">

					<div class="form-group">
						<select class="wf-filter-part-id cov-filter select2 form-control" style="width: 250px;"></select>
					</div>

					<div class="form-group">
						<select style="display: none" class="wf-filter-model-id cov-filter form-control "></select>
					</div>

					<div class="form-group">
						<select style="display: none" class="wf-filter-unit-id cov-filter form-control "></select>
					</div>

					<button class="wf-btn-filter" style="display: none">Find</button>

				</div>

			</td>
			<td class="main-stock-td"></td>
            <td class="main-stock-td"></td>
            <td class="main-stock-td"></td>
            <td class="main-stock-td"></td>
            <td class="main-stock-td"></td>
			<!-- <td colspan="4" class="wd_title"><?= Yii::t('app', 'Total DOH ($)') ?>:</td> -->
			<td class="total_doh_sum_import" style="text-align: right;font-weight: bold" title="1 USD: <?= Helpers::formatRemoveDecimal($rateUSD) ?>, 1 EUR: <?= Helpers::formatRemoveDecimal($rateEUR) ?>, 1 RUB: <?= Helpers::formatRemoveDecimal($rateRUB) ?>"><?= $loading ?></td>
			<? foreach ($period_weekly as $col => $per) { ?>
				<td class="wd" id="w_<?= ($col + 1) ?>"><?= $loading ?></td>
			<? } ?>
		</tr>

		<tr class="tr_head">
			<th style="width: 30px;" class="text-center">№</th>
			<th style="width: 100px;" class="text-left"><?= Yii::t('app', 'Part') ?></th>
			<th style="width: 120px;" class="text-left"><?= Yii::t('app', 'Part color') ?></th>
			<th><?= Yii::t('app', 'Part name') ?></th>
			<th style="width: 100px;" class="text-center"><?= Yii::t('app', 'Type') ?></th>
			<th style="width: 100px;" class="text-center"><?= Yii::t('app', 'Unit') ?></th>

			<th style="width: 90px;" class="text-right main-stock"><?= Yii::t('app', 'WH balance') ?></th>

			<th style="width: 90px;" class="text-right stock"><?= Yii::t('app', 'Line balance') ?></th>
			<th style="width: 90px;" class="text-right stock"><?= Yii::t('app', 'Semi balance') ?></th>
			<th style="width: 90px;" class="text-right stock"><?= Yii::t('app', 'Pending balance') ?></th>
			<th style="width: 90px;" class="text-right stock"><?= Yii::t('app', 'Outsourcing balance') ?></th>
			<th style="width: 90px;" class="text-right stock"><?= Yii::t('app', 'Arrived') ?></th>

			<th style="width: 90px;" class="text-right"><?= Yii::t('app', 'Total balance') ?></th>
			<!-- <th style="width: 90px;" class="text-right qty-fg"><?= Yii::t('app', 'FG balance') ?></th> -->
			<!-- <th style="width: 60px;" class="text-center"><?= Yii::t('app', 'DOH (days)') ?></th>
			<th style="width: 60px;" class="text-center"><?= Yii::t('app', 'DOH ($)') ?></th> -->
			<? foreach ($period_weekly as $col => $per) { ?>
				<th style="width: 90px;" class="text-center"><?= ((strlen(trim($per['plandate'])) > 7)) ? date("d.m", strtotime($per['from'])) . '<br>-<br>' . date("d.m", strtotime($per['to'])) : date("m.Y", strtotime($per['plandate'])) ?></th>
				<?
				$w[$col + 1] = 0;
				?>
			<? } ?>
		</tr>
	</thead>
	<tbody>
		<?
		$i = 0;
		$filter['parts'] = [];
		$filter['models'] = [];
		$filter['units'] = [];
		?>
		<? foreach ($data_weekly as $row) {
			$part = Part::find()
			->where(['id' => $row['part_id']])->one();
			$actualContract = $part->getActualContract();
			$price = $actualContract->price ?? 0;
			$currency = $actualContract->contract->currency->code ?? '';
			$priceUSD = 0;
			switch ($currency) {
				case 'EUR':
					$priceUSD = ($rateUSD > 0) ? $price * $rateEUR / $rateUSD : 0;
					break;
				case 'RUB':
					$priceUSD = ($rateUSD > 0) ? $price * $rateRUB / $rateUSD : 0;;
					break;
				case 'UZS':
					$priceUSD = ($rateUSD > 0) ? $price / $rateUSD: 0;
					break;
				case 'USD':
					$priceUSD = $price;
					break;
			}
			$i++; ?>
			<tr <?= ($i % 2 == 0) ? 'class="tr-item tr_odd"' : 'class="tr-item"' ?> data-part-id="<?= $row['part_id'] ?>" data-model-id="" data-unit-id="<?= $row['unit_id'] ?>">
				<td class="text-center num"><?= $i ?></td>
				<td class="text-left">
					<? require '__form-pop.php' ?>
				</td>
				<td class="text-left td-nowrap" style="width: 120px;" title="<?= $row['remark'] ?>"><?= $row['part_no'] ?></td>
				<td style="max-width: 150px;" class="td-nowrap" title="<?= $row['comment'] ?>"><?= mb_strtoupper($row['part_name']) ?></td>
				<td class="text-center"><?= $row['csourse'] ?></td>
				<td class="text-center"><?= $row['unit'] ?></td>


				<td class="text-right <? if ($row['whbal'] < 0) echo "qty-red";
															elseif ($row['whbal'] == 0) echo "qty-zero"; ?>"><?= Helpers::formatRemoveDecimal($row['whbal']) ?></td>
				<td class="text-right stock <? if ($row['linebal'] < 0) echo "qty-red";
															elseif ($row['linebal'] == 0) echo "qty-zero"; ?>"><?= Helpers::formatRemoveDecimal($row['linebal']) ?></td>
				<td class="text-right stock <? if ($row['semistock'] < 0) echo "qty-red";
															elseif ($row['semistock'] == 0) echo "qty-zero"; ?>"><?= Helpers::formatRemoveDecimal($row['semistock']) ?></td>
				<td class="text-right stock <? if ($row['pending'] < 0) echo "qty-red";
															elseif ($row['pending'] == 0) echo "qty-zero"; ?>"><?= Helpers::formatRemoveDecimal($row['pending']) ?></td>
				<td class="text-right stock <? if ($row['outsourcing'] < 0) echo "qty-red";
															elseif ($row['outsourcing'] == 0) echo "qty-zero"; ?>"><?= Helpers::formatRemoveDecimal($row['outsourcing']) ?></td>
				<td class="text-right stock <? if ($row['arrive'] < 0) echo "qty-red";
															elseif ($row['arrive'] == 0) echo "qty-zero"; ?>"><?= Helpers::formatRemoveDecimal($row['arrive']) ?></td>


				<td class="text-right <? if ($row['totalstock'] < 0) echo "qty-red";
															elseif ($row['totalstock'] == 0) echo "qty-zero"; ?>"><?= Helpers::formatRemoveDecimal($row['totalstock']) ?></td>
				<!-- <td class="text-right <? if ($row['fgstock'] < 0) echo "qty-red";
															elseif ($row['fgstock'] == 0) echo "qty-zero";
															else echo "qty-fg"; ?>"><?= Helpers::formatRemoveDecimal($row['fgstock']) ?></td> -->
				<!-- <td class="text-center"><?= $row['doh'] ?></td> -->
				<!-- <td class="text-right" title="<?= Helpers::formatRemoveDecimal($price) ?> <?= $currency ?>"><?= ($row['totalstock'] > 0) ? Helpers::formatRemoveDecimal($doh_sum) : 0 ?></td> -->

				<? foreach ($period_weekly as $col => $per) { ?>
					<td class="text-right  <? if ($row['col' . ($col + 1)] < 0) echo 'req-red';
																	elseif ($row['col' . ($col + 1)] == 0) echo "qty-zero"; ?>"><?= Helpers::formatRemoveDecimal($row['col' . ($col + 1)]) ?></td>
					<?
					if ($row['col' . ($col + 1)] < 0) {
						$w[$col + 1] = $w[$col + 1] + 1;
					}
					?>
				<? } ?>

			</tr>
			<? $calc_at = date("d.m.Y H:i", strtotime($row['calc_at'])); ?>
			<?
			// filter uchun arraylar hosil qilish
			$filter['parts'][] = $row['part_id'] . '|' . $part->partinfo;
			$filter['units'][] = $row['unit_id'] . '|' . $row['unit'];
			?>
		<? } ?>
		<?
		$filter['parts'] = array_unique($filter['parts']);
		$filter['models'] = array_unique($filter['models']);
		$filter['units'] = array_unique($filter['units']);
		$fparts = [];
		$fmodels = [];
		$funits = [];
		foreach ($filter['parts'] as $row) {
			list($id, $value) = explode('|', $row);
			$fparts[$id] = $value;
		}
		foreach ($filter['models'] as $row) {
			list($id, $value) = explode('|', $row);
			$fmodels[$id] = $value;
		}
		foreach ($filter['units'] as $row) {
			list($id, $value) = explode('|', $row);
			$funits[$id] = $value;
		}
		?>
	</tbody>
</table>
<div style="display: none">
	<p id="wf-parts"><?= json_encode($fparts) ?></p>
	<p id="wf-models"><?= json_encode($fmodels) ?></p>
	<p id="wf-units"><?= json_encode($funits) ?></p>
	<p id="wf-label-parts"><?= Yii::t('app', 'All parts'); ?></p>
	<p id="wf-label-models"><?= Yii::t('app', 'All models'); ?></p>
	<p id="wf-label-units"><?= Yii::t('app', 'All units'); ?></p>
</div>

<?php
$docReadyJs = <<< JS

$(document).ready(function() {

	// Select larni to'ldirish
	var parts = $.parseJSON($('#wf-parts').html());
	var models = $.parseJSON($('#wf-models').html());
	var units = $.parseJSON($('#wf-units').html());

	fillSelect($('.wf-filter-part-id'), parts, $('#wf-label-parts').html());
	fillSelect($('.wf-filter-model-id'), models, $('#wf-label-models').html());
	fillSelect($('.wf-filter-unit-id'), units, $('#wf-label-units').html());

	function fillSelect(el,data,all_text){
		el.html('');
		el.append(new Option(all_text, ''));
		$.each(data, function(k, v) {
			el.append(new Option(v, k));
		});
	}

	$('.wf-div-filter').show();
	// ***

	// Select tanlanganda filterni ishlatish
	$('.wf-filter-part-id').on('change', function () {
		$('.wf-btn-filter').trigger('click');
	});

	$('.wf-filter-model-id').on('change', function () {
		$('.wf-btn-filter').trigger('click');
	});

	$('.wf-filter-unit-id').on('change', function () {
		$('.wf-btn-filter').trigger('click');
	});
	// ***

	// Asosiy filter funksiyasi
	$('.wf-btn-filter').on('click', function () {

    var filter_part_id = $('.wf-filter-part-id').val();
		var filter_model_id = $('.wf-filter-model-id').val();
		var filter_unit_id = $('.wf-filter-unit-id').val();
		var ii = 0;
		var is_left = true;

		$('#fix_table_w tr.tr-item').each(function (i, row) {
			$(this).show(); 
		});

		$('#fix_table_w tr.tr-item').each(function (i, row) {

			is_left = true;
			$(this).removeClass('tr_odd');
			$(this).find('td').css("background-color", "");

			if(filter_part_id != ''){
				if($(this).attr('data-part-id') != filter_part_id){
					$(this).hide(); 
					is_left = false;
				}	
			}

			if(filter_model_id != ''){
				if($(this).attr('data-model-id') != filter_model_id){
					$(this).hide(); 
					is_left = false;
				}	
			}
		
			if(filter_unit_id != ''){
				if($(this).attr('data-unit-id') != filter_unit_id){
					$(this).hide(); 
					is_left = false;
				}
			}
			if(is_left) {
				ii++;
				if(ii%2 == 0) {
					$(this).addClass('tr_odd');
					$(this).find('td').css("background-color", "#e8e1e1");
				}else{
					
					$(this).find('td').css("background-color", "#fff");
				}
			}

			$(this).find('td.num').html(ii);

		});

	});
	// ***

}) 
JS;

$this->registerJs($docReadyJs);
?>