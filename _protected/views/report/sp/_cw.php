<?php

  use app\components\Helpers;
  use yii\helpers\Html;
  use yii\helpers\Url;
  
?>

<table id="main-table" class="tablesorter">
  <thead>

    <tr>
      <th rowspan="2" style="text-align: center;font-size: 12px;padding: 6px;">
        <div style="width: 45px;">

          <?= Yii::t('app', 'CW') ?> <?= date("Y") ?>
        </div>
      </th>
      <th colspan="6" style="text-align: center;font-size: 12px;padding: 6px;">
        <?= Yii::t('app', 'Shipment performance (No of parts)') ?>
      </th>
      <th colspan="7" style="text-align: center;font-size: 12px;padding: 6px;">
        <?= Yii::t('app', 'Shipment performance (Amount)') ?>
      </th>
    </tr>

    <tr>

      <th style="text-align: center;font-size: 12px;padding: 6px;">
        <div style="width: 70px;">
          <?= Yii::t('app', 'Planned <br>< {less_dates_count} DOH', ['less_dates_count' => Yii::$app->params['less_dates_count']]) ?>
        </div>
      </th>
      <th style="text-align: center;font-size: 12px;padding: 6px;">
        <div style="width: 70px;">
          <?= Yii::t('app', 'Shipped') ?>
        </div>
      </th>
      <th style="text-align: center;font-size: 12px;padding: 6px;">
        <div style="width: 70px;">
          <?= Yii::t('app', 'OK') ?>
        </div>
      </th>
      <th style="text-align: center;font-size: 12px;padding: 6px;">
        <div style="width: 70px;">
          <?= Yii::t('app', 'Under shipped') ?>
        </div>
      </th>
      <th style="text-align: center;font-size: 12px;padding: 6px;">
        <div style="width: 70px;">
          <?= Yii::t('app', 'Over shipped') ?>
        </div>
      </th>
      <th style="text-align: center;font-size: 12px;padding: 6px;">
        <div style="width: 70px;">
          <?= Yii::t('app', 'Not <br> shipped') ?>
        </div>
      </th>


      <th style="text-align: center;font-size: 12px;padding: 6px;">
        <div style="width: 70px;">
          <?= Yii::t('app', 'Planned <br>< {less_dates_count} DOH', ['less_dates_count' => Yii::$app->params['less_dates_count']]) ?>
        </div>
      </th>
      <th style="text-align: center;font-size: 12px;padding: 6px;">
        <div style="width: 70px;">
          <?= Yii::t('app', 'Shipped') ?>
        </div>
      </th>
      <th style="text-align: center;font-size: 12px;padding: 6px;">
        <div style="width: 70px;">
          <?= Yii::t('app', 'OK') ?>
        </div>
      </th>
      <th style="text-align: center;font-size: 12px;padding: 6px;">
        <div style="width: 70px;">
          <?= Yii::t('app', 'Under shipped') ?>
        </div>
      </th>
      <th style="text-align: center;font-size: 12px;padding: 6px;">
        <div style="width: 70px;">
          <?= Yii::t('app', 'Over shipped') ?>
        </div>
      </th>
      <th style="text-align: center;font-size: 12px;padding: 6px;">
        <div style="width: 70px;">
          <?= Yii::t('app', 'Not <br> shipped') ?>
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


    <? foreach ($data as $row) {?>


    <tr>
      <td>
        <div style="text-align: center;width: 45px;">
          <?= Html::a($row['cw'], Url::toRoute(['report/sp','sp_id' => $row['sp_id']]))  ?>
        </div>
      </td>
      <td>
        <div style="text-align: right;" class="<?if($row['all'] == 0) echo " zero"?>">
          <?= Helpers::numberFormatRemoveZero($row['all'], 0, '.', ' ', true, true) ?>
        </div>
      </td>
      <td>
        <div style="text-align: right;" class="<?if($row['shipped'] == 0) echo " zero"?>">
          <?= Helpers::numberFormatRemoveZero($row['shipped'], 0, '.', ' ', true, true) ?>
        </div>
      </td>
      <td>
        <div style="text-align: right;" class="<?if($row['ok'] == 0) echo " zero"?>">
          <?= Helpers::numberFormatRemoveZero($row['ok'], 0, '.', ' ', true, true) ?>
        </div>
      </td>
      <td>
        <div style="text-align: right;" class="<?if($row['under'] == 0) echo " zero"?>">
          <?= Helpers::numberFormatRemoveZero($row['under'], 0, '.', ' ', true, true) ?>
        </div>
      </td>
      <td>
        <div style="text-align: right;" class="<?if($row['over'] == 0) echo " zero"?>">
          <?= Helpers::numberFormatRemoveZero($row['over'], 0, '.', ' ', true, true) ?>
        </div>
      </td>
      <td>
        <div style="text-align: right;" class="<?if($row['not_shipped'] == 0) echo " zero"?>">
          <?= Helpers::numberFormatRemoveZero($row['not_shipped'], 0, '.', ' ', true, true) ?>
        </div>
      </td>

      <td>
        <div style="text-align: right;" class="<?if($row['all_amount'] == 0) echo " zero"?>">
          <?= Helpers::numberFormatRemoveZero($row['all_amount'], 0, '.', ' ', true, true) ?>
        </div>
      </td>
      <td>
        <div style="text-align: right;" class="<?if($row['shipped_amount'] == 0) echo " zero"?>">
          <?= Helpers::numberFormatRemoveZero($row['shipped_amount'], 0, '.', ' ', true, true) ?>
        </div>
      </td>
      <td>
        <div style="text-align: right;" class="<?if($row['ok_amount'] == 0) echo " zero"?>">
          <?= Helpers::numberFormatRemoveZero($row['ok_amount'], 0, '.', ' ', true, true) ?>
        </div>
      </td>
      <td>
        <div style="text-align: right;" class="<?if($row['under_amount'] == 0) echo " zero"?>">
          <?= Helpers::numberFormatRemoveZero($row['under_amount'], 0, '.', ' ', true, true) ?>
        </div>
      </td>
      <td>
        <div style="text-align: right;" class="<?if($row['over_amount'] == 0) echo " zero"?>">
          <?= Helpers::numberFormatRemoveZero($row['over_amount'], 0, '.', ' ', true, true) ?>
        </div>
      </td>
      <td>
        <div style="text-align: right;" class="<?if($row['not_shipped_amount'] == 0) echo " zero"?>">
          <?= Helpers::numberFormatRemoveZero($row['not_shipped_amount'], 0, '.', ' ', true, true) ?>
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