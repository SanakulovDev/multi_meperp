<?php
  use app\components\Helpers;

?>

<? $this->title = $data['header']; ?>

<table id="main-table" class="tablesorter">
  <thead>

    <tr>
      <th rowspan="2" style="text-align: center;font-size: 12px;padding: 6px;">
        <div style="width: 150px;">
          <?= Yii::t('app', 'Part number') ?>
        </div>
      </th>
      <th rowspan="2" style="text-align: center;font-size: 12px;padding: 6px;">
        <div style="width: 250px;">
          <?= Yii::t('app', 'Part name') ?>
        </div>
      </th>
      <th rowspan="2" style="text-align: center;font-size: 12px;padding: 6px;">
        <div style="width: 70px;">
          <?= Yii::t('app', 'DOH') ?>
        </div>
      </th>
      <th colspan="5" style="text-align: center;font-size: 12px;padding: 6px;">
        <?= Yii::t('app', 'Shipment performance < {less_dates_count} DOH', ['less_dates_count' => Yii::$app->params['less_dates_count']]) ?>
      </th>
      <th colspan="4" style="text-align: center;font-size: 12px;padding: 6px;">
        <?= Yii::t('app', 'Shipment performance (Amount)') ?>
      </th>
    </tr>

    <tr>

      <th style="text-align: center;font-size: 12px;padding: 6px;">
        <div style="width: 70px;">
          <?= Yii::t('app', 'Shortage') ?>
        </div>
      </th>
      <th style="text-align: center;font-size: 12px;padding: 6px;">
        <div style="width: 70px;">
          <?= Yii::t('app', 'To ship quantity') ?>
        </div>
      </th>
      <th style="text-align: center;font-size: 12px;padding: 6px;">
        <div style="width: 70px;">
          <?= Yii::t('app', 'Shipped quantity') ?>
        </div>
      </th>
      <th style="text-align: center;font-size: 12px;padding: 6px;">
        <div style="width: 70px;">
          <?= Yii::t('app', 'Balance quantity') ?>
        </div>
      </th>
      <th style="text-align: center;font-size: 12px;padding: 6px;">
        <div style="width: 70px;">
          <?= Yii::t('app', 'Percent (%)') ?>
        </div>
      </th>

      <th style="text-align: center;font-size: 12px;padding: 6px;">
        <div style="width: 70px;">
          <?= Yii::t('app', 'To ship quantity') ?>
        </div>
      </th>
      <th style="text-align: center;font-size: 12px;padding: 6px;">
        <div style="width: 70px;">
          <?= Yii::t('app', 'Shipped quantity') ?>
        </div>
      </th>
      <th style="text-align: center;font-size: 12px;padding: 6px;">
        <div style="width: 70px;">
          <?= Yii::t('app', 'Balance quantity') ?>
        </div>
      </th>

      <th style="text-align: center;font-size: 12px;padding: 6px;">
        <div style="width: 70px;">
          <?= Yii::t('app', 'Amount <br>> {greater_dates_count} DOH', ['greater_dates_count' => Yii::$app->params['greater_dates_count']]) ?>
        </div>
      </th>

    </tr>

  </thead>
  <tbody>


    <? foreach ($data['parts'] as $row) {?>


    <tr>
      <td>
        <div style="overflow: hidden;text-overflow: ellipsis;white-space: nowrap;width: 150px;" title="<?= htmlspecialchars($row['part_no']) ?>">
          <?= htmlspecialchars($row['part_no']) ?>
        </div>
      </td>
      <td>
        <div style="overflow: hidden;text-overflow: ellipsis;white-space: nowrap;width: 250px;" title="<?= htmlspecialchars($row['part_name']) ?>">
          <?= htmlspecialchars($row['part_name']) ?>
        </div>
      </td>
      <td>
        <div style="text-align: center;" class="<?if($row['doh'] == 0) echo " zero"?>">
          <?= Helpers::numberFormatRemoveZero($row['doh'], 0, '.', ' ', true, true) ?>
        </div>
      </td>
      <td>
        <div style="text-align: right;" class="<?if($row['shortage'] == 0) echo " zero"?>">
          <?= Helpers::numberFormatRemoveZero($row['shortage'], 0, '.', ' ', true, true) ?>
        </div>
      </td>
      <td>
        <div style="text-align: right;" class="<?if($row['to_ship'] == 0) echo " zero"?>">
          <?= Helpers::numberFormatRemoveZero($row['to_ship'], 0, '.', ' ', true, true) ?>
        </div>
      </td>
      <td>
        <div style="text-align: right;" class="<?if($row['shipped'] == 0) echo " zero"?>">
          <?= Helpers::numberFormatRemoveZero($row['shipped'], 0, '.', ' ', true, true) ?>
        </div>
      </td>
      <td>
        <div style="text-align: right;" class="<? if($row['balance'] == 0) {echo " zero";} elseif($row['balance'] < 0) {echo " red";}?>">
          <?= Helpers::numberFormatRemoveZero($row['balance'], 0, '.', ' ', true, true) ?>
        </div>
      </td>

      <td>
        <div style="text-align: right;" class="<?if($row['percent'] == 0) echo " zero"?>">
          <?= Helpers::numberFormatRemoveZero($row['percent'], 0, '.', ' ', true, true) ?>
        </div>
      </td>
      <td>
        <div style="text-align: right;" class="<?if($row['to_ship_amount'] == 0) echo " zero"?>">
          <?= Helpers::numberFormatRemoveZero($row['to_ship_amount'], 0, '.', ' ', true, true) ?>
        </div>
      </td>
      <td>
        <div style="text-align: right;" class="<?if($row['shipped_amount'] == 0) echo " zero"?>">
          <?= Helpers::numberFormatRemoveZero($row['shipped_amount'], 0, '.', ' ', true, true) ?>
        </div>
      </td>
      <td>
        <div style="text-align: right;" class="<? if($row['balance_amount'] == 0) {echo " zero";} elseif($row['balance_amount'] < 0) {echo " red";}?>">
          <?= Helpers::numberFormatRemoveZero($row['balance_amount'], 0, '.', ' ', true, true) ?>
        </div>
      </td>
      <td>
        <div style="text-align: right;" class="<?if($row['over_doh_amount'] == 0) echo " zero"?>">
          <?= Helpers::numberFormatRemoveZero($row['over_doh_amount'], 0, '.', ' ', true, true) ?>
        </div>
      </td>
    </tr>


    <?}?>



  </tbody>




</table>