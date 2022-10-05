<!-- oem start -->
<?
use yii\helpers\Html;
use app\components\Helpers;
$downloadFileName = Helpers::downloadFileName('oem-daily', '1');
$downloadFileName = rtrim($downloadFileName, '.1');
?>
    
    <tr>
      <td colspan="<?=count($period_daily_veh) + 2?>" style="height: 30px;">
        <b><?=Yii::t('app', 'OEM Production plan');?></b>
      </td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    <tr class="tr_head">
      <th style="width: 30px;" class="text-center">№</th>
			<th style="width: 100px;" class="text-left"><?= Yii::t('app', 'Model') ?></th>
      <th class="text-center"></th>
      <th class="text-center"></th>
      <th class="text-center"></th>
      <th class="text-center"></th>
      <th class="text-center"></th>
      <th class="text-center"></th>
			<? foreach ($period_daily_veh as $col => $pdate) { ?>
				<th style="width: 90px;" class="text-center"><?= date("d.m", strtotime($pdate)) ?></th>
			<? } ?>
		</tr>

    <?
      $i = 1;
      foreach($models as $model){
    ?>
		<tr>
      <td style="width: 30px;" class="text-center"><?=$i++?></td>
			<td style="width: 100px;" class="text-left"><?=$model['description']?></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
			<? foreach ($period_daily_veh as $col => $pdate) { ?>
        <? $qty = $this->context->getVehicleDataByDate($data_vehicle_oem,$model->id,$pdate);?>
				<td style="width: 90px;" class="text-center <? if ($qty == 0) echo 'qty-zero';?>"><?=$qty?></td>
			<? } ?>
    </tr>
    <?}?>
     
    

    <!-- oem end -->
    
