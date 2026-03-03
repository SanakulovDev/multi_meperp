<?
use app\components\Helpers;
?>


<table id="fix_table_dv" class="table table-req">
  <thead>

  <tr class="tr_minus_count">
    <td colspan="8"></td>
    <td colspan="<?=count($period_daily_veh)?>" class="wd_title" style="text-align: left;"><?= Yii::t('app', 'No of short models') ?></td>
  </tr>
  <tr class="tr_minus_count">
    <td colspan="8" class="wd_title"></td>
    <? foreach ($period_daily_veh as $col => $pdate) { ?>
      <td class="wd" id="dv_<?= ($col + 1) ?>"><?= $loading ?></td>
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
    <? foreach ($period_daily_veh as $col => $pdate) { ?>
      <th style="width: 90px;" class="text-center"><?= date("d.m", strtotime($pdate)) ?><br><span style="color: gray"><?=($col+1)?></span></th>
      <?
      $dv[$col + 1] = 0;
      ?>
    <? } ?>
  </tr>
  </thead>
  <tbody>
  <? $i = 0; ?>
  <?
  foreach ($data_vehicle_daily as $row) {
    $i++;
    ?>
    <tr <?= ($i % 2 == 0) ? 'class="tr_odd"' : '' ?>>
      <td class="text-center"><?= $i ?></td>
      <td style="width: 100px;" class="text-left td-nowrap"><?= $row['model_desc'] ?></td>
      <td style="width: 70px;" class="text-right td-nowrap <? if ($row['stock'] < 0) echo "qty-red";
      elseif ($row['stock'] == 0) echo "qty-zero"; ?>"><?= Helpers::formatRemoveDecimal($row['stock']) ?></td>
      <td style="width: 70px;" class="text-right td-nowrap <? if ($row['uamstock'] < 0) echo "qty-red";
      elseif ($row['uamstock'] == 0) echo "qty-zero"; ?>"><?= Helpers::formatRemoveDecimal($row['uamstock']) ?></td>
      <td style="width: 70px;" class="text-right td-nowrap <? if ($row['intransit'] < 0) echo "qty-red";
      elseif ($row['intransit'] == 0) echo "qty-zero"; ?>"><?= Helpers::formatRemoveDecimal($row['intransit']) ?></td>
      <td style="width: 70px;" class="text-right td-nowrap <? if ($row['orders'] < 0) echo "qty-red";
      elseif ($row['orders'] == 0) echo "qty-zero"; ?>"><?= Helpers::formatRemoveDecimal($row['orders']) ?></td>
      <td style="width: 70px;" class="text-right td-nowrap <? if ($row['totalpl'] < 0) echo "qty-red";
      elseif ($row['totalpl'] == 0) echo "qty-zero"; ?>"><?= Helpers::formatRemoveDecimal($row['totalpl']) ?></td>
      <td style="width: 60px;" class="text-right td-nowrap <? if ($row['doh'] < 0) echo "qty-red";
      elseif ($row['doh'] == 0) echo "qty-zero"; ?>"><?= $row['doh'] ?></td>
      <td  style="width: 60px;" class="text-center td-nowrap "><?= ($row['stock_out']) ? date('d M',strtotime($row['stock_out'])) : ''?></td>

      <? foreach ($period_daily_veh as $col => $pdate) { ?>
        <td class="text-right  <? if ($row['col' . ($col + 1)] < 0) echo 'req-red';
        elseif ($row['col' . ($col + 1)] == 0) echo "qty-zero"; ?>"><?= Helpers::formatRemoveDecimal($row['col' . ($col + 1)]) ?></td>
        <?
        if ($row['col' . ($col + 1)] < 0) {
          $dv[$col + 1] = $dv[$col + 1] + 1;
        }
        ?>
      <? } ?>

    </tr>
    <? $calc_at = date("d.m.Y H:i", strtotime($row['calc_at'])); ?>
  <? } ?>

  <? require_once '__oem_daily.php'; ?>
  <? require_once '__intransit_daily.php'; ?>


  </tbody>
</table>



