<?
use app\components\Helpers;
?>


<table class="table table-req" id="fix_table_wv">
	
	<thead>
		<tr class="tr_minus_count">
			<td colspan="8"></td>
			<td colspan="100" class="wd_title" style="text-align: left;"><?= Yii::t('app', 'No of short models') ?></td>
		</tr>
		<tr class="tr_minus_count">
			<td colspan="8" class="wd_title"></td>
			<? foreach ($period_weekly_veh as $col => $per) { ?>
				<td class="wd" id="wv_<?= ($col + 1) ?>"><?= $loading ?></td>
			<? } ?>
		</tr>
		<tr class="tr_head">
			<th style="width: 30px;" class="text-center">№</th>
			<th style="width: 100px;" class="text-left"><?= Yii::t('app', 'Model') ?></th>
			<th style="width: 70px;" class="text-right"><?= Yii::t('app', 'Vehicle set stock') ?></th>
			<th style="width: 70px;" class="text-right"><?= Yii::t('app', 'Stock in UzAutoM') ?></th>
			<th style="width: 70px;" class="text-right"><?= Yii::t('app', 'TTL Intransit') ?></th>
			<th style="width: 70px;" class="text-right"><?= Yii::t('app', 'Paid but not shipped orders') ?></th>
			<th style="width: 70px;" class="text-right"><?= Yii::t('app', 'Total pipeline') ?></th>
			<th style="width: 60px;" class="text-center"><?= Yii::t('app', 'DOH') ?></th>
			<th style="width: 60px;" class="text-center"><?= Yii::t('app', 'Stock out') ?></th>
			<? foreach ($period_weekly_veh as $col => $per) { ?>
				<th style="width: 90px;" class="text-center"><?= ((strlen(trim($per['plandate'])) > 7)) ? date("d.m", strtotime($per['from'])) . '<br>-<br>' . date("d.m", strtotime($per['to'])) : date("m.Y", strtotime($per['plandate'])) ?></th>
				<?
				$wv[$col + 1] = 0;
				?>
			<? } ?>
		</tr>
	</thead>
	<tbody>
		<? $i = 0; ?>
		<? 
			foreach ($data_vehicle_weekly as $row) {
			$i++; 
		?>
			<tr <?= ($i % 2 == 0) ? 'class="tr_odd"' : '' ?>>
				<td class="text-center"><?= $i ?></td>
				<td class="text-left"><?= $row['model_desc'] ?></td>
				<td class="text-right <? if ($row['stock'] < 0) echo "qty-red";
										elseif ($row['stock'] == 0) echo "qty-zero"; ?>"><?= Helpers::formatRemoveDecimal($row['stock']) ?></td>
				<td class="text-right <? if ($row['uamstock'] < 0) echo "qty-red";
										elseif ($row['uamstock'] == 0) echo "qty-zero"; ?>"><?= Helpers::formatRemoveDecimal($row['uamstock']) ?></td>
				<td class="text-right <? if ($row['intransit'] < 0) echo "qty-red";
										elseif ($row['intransit'] == 0) echo "qty-zero"; ?>"><?= Helpers::formatRemoveDecimal($row['intransit']) ?></td>
				<td class="text-right <? if ($row['orders'] < 0) echo "qty-red";
										elseif ($row['orders'] == 0) echo "qty-zero"; ?>"><?= Helpers::formatRemoveDecimal($row['orders']) ?></td>
				<td class="text-right <? if ($row['totalpl'] < 0) echo "qty-red";
										elseif ($row['totalpl'] == 0) echo "qty-zero"; ?>"><?= Helpers::formatRemoveDecimal($row['totalpl']) ?></td>
				<td class="text-right <? if ($row['doh'] < 0) echo "qty-red";
										elseif ($row['doh'] == 0) echo "qty-zero"; ?>"><?= $row['doh'] ?></td>
				<td class="text-center"><?= ($row['stock_out']) ? date('d M',strtotime($row['stock_out'])) : ''?></td>
				
				<? foreach ($period_weekly_veh as $col => $per) { ?>
					<td class="text-right  <? if ($row['col' . ($col + 1)] < 0) echo 'req-red';
											elseif ($row['col' . ($col + 1)] == 0) echo "qty-zero"; ?>"><?= Helpers::formatRemoveDecimal($row['col' . ($col + 1)]) ?></td>
					<?
					if ($row['col' . ($col + 1)] < 0) {
						$wv[$col + 1] = $wv[$col + 1] + 1;
					}
					?>
				<? } ?>

			</tr>
			<? $calc_at = date("d.m.Y H:i", strtotime($row['calc_at'])); ?>
		<? } ?>

		<? require_once '__oem_weekly.php'; ?>
		<? require_once '__intransit_weekly.php'; ?>

	</tbody>
</table>

