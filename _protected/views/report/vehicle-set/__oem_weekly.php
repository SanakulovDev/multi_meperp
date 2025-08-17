<!-- oem start -->
 
    <tr>
      <td colspan="<?=count($period_weekly_veh) + 2?>" style="height: 30px;"><b><?=Yii::t('app', 'OEM Production plan');?></b></td>
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
			<? foreach ($period_weekly_veh as $col => $per) { ?>
				<th style="width: 90px;" class="text-center"><?= ((strlen(trim($per['plandate'])) > 7)) ? date("d.m", strtotime($per['from'])) . '<br>-<br>' . date("d.m", strtotime($per['to'])) : date("m.Y", strtotime($per['plandate'])) ?></th>
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
			<? foreach ($period_weekly_veh as $col => $per) { ?>
        <? $qty = $this->context->getVehicleDataByDate($data_vehicle_oem,$model->id,$per['from'],$per['to']);?>
				<td style="width: 90px;" class="text-center <? if ($qty == 0) echo 'qty-zero';?>"><?=$qty?></td>
			<? } ?>
    </tr>
    <?}?>
    
		<!-- oem end -->