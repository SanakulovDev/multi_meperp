<?php
use app\assets\AdminLteAsset;
use app\components\Helpers;
use yii\helpers\Html;
use yii\web\JqueryAsset;
use yii\web\YiiAsset;

/* @var $this yii\web\View */
/* @var $model app\models\Waybill */
/* @var $details */
$this->title = $contract;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Waybill'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);

$i=0;
  $this->registerCssFile("@themes/css/fg_invoice_print.css", ['depends' => [AdminLteAsset::className()]]);
  $this->registerJsFile("@themes/js/printThis.js", ['depends' => [JqueryAsset::className()]]);
?>
  <div class="clearfix"></div>
  <div class="row" id="print_div" align="center" style=" margin-right:5px">
    <table class="inv_detail">
      <tr height="22">
        <td colspan="9" class="font_13 txt_center font_bold v_top">
            Реестр контрактов <?= $contract?> </br> Счёт фактура (<?= implode(',', $result['waybill_no'])?>)
        </td>
      </tr>
      <tr height="37">
          <td colspan="4" class="font_9 txt_left v_top">
            Покупатель:<span class="font_9 font_underline font_bold">
              <?=$result['customer']?>
            </span>
          </td>
      </tr>
      <tr height="55">
        <td colspan="4" class="adress_rekvizit">
          Адрес:<span class="font_10 font_underline font_bold">
            <?=$customer->address;?>
          </span>
        </td>
      </tr>
      <tr height="37">
        <td colspan="4" class="adress_rekvizit">Регистрационный код плательщика<br> НДС:
          <span class="font_10 font_underline font_bold"><?=$customer->vat?></span>
        </td>
        <td></td>
        <td colspan="4" class="adress_rekvizit">
          <span class="font_8">ИНН покупателя: </span>
          <span class="font_10 font_underline font_bold"><?=$customer->tin?></span>
        </td>
      </tr>
    </table >

          <table class="inv_detail ">
            <tbody>
                <tr height="20" class="tr_border_thin font_bold txt_center">
                    <td rowspan="2" class="font_8 th_cyanHeader" style="width: 30px;">№</td>
                    <td rowspan="2" colspan="2" class="font_8 th_cyanHeader">Счёт фактура</td>
                    <td rowspan="2" class="font_8 th_cyanHeader" style="width: 40px;">Ед.<br>изм</td>
                    <td rowspan="2" class="font_8 th_cyanHeader" style="width: 60px;">Кол-во</td>
                    <td rowspan="2" class="font_8 th_cyanHeader" style="width: 70px;">Цена</td>
                    <td rowspan="2" class="font_8 th_cyanHeader" style="width: 100px;">Стоимость<br>поставки</td>
                    <td colspan="2" class="font_8 th_cyanHeader" style="width: 140px;">НДС</td>
                    <td rowspan="2" class="font_8 th_cyanHeader" style="width: 100px;">Стоимость<br>поставки с<br>учетом НДС</td>
                </tr>
                <tr height="31" class="tr_border_thin font_bold txt_center">
                    <td class="font_8 th_cyanHeader" style="width: 30%;">Став-<br>ка %</td>
                    <td class="font_8 th_cyanHeader" style="width: 70%;">Сумма</td>
                </tr>
                <tr class="font_8 txt_center v_middle font_bold tr_border_thin">
                    <td>№</td>
                    <td colspan="2">1</td>
                    <td>2</td>
                    <td>3</td>
                    <td>4</td>
                    <td>5</td>
                    <td>6</td>
                    <td>7</td>
                    <td>8</td>
                </tr>
                  <?php if(isset($details)):?>
                    <?php 
                      $tno = 0;
                      $all_qty = 0;
                      $all_amount = 0;
                      $all_vat_amount = 0;
                      $all_amount_with_vat = 0;
                      $pg_num = 1;
                      $pg_row_cnt = 0;
                      ?>
                    <?php foreach($details as  $items):?>
                      <?php foreach($items as $detail):?>
                        <?php
                          $tno++;
                          $vat = $firstFgInvoice->vat;
                          $ptno = "Полимерный компаунд ".$detail['part_name'] . "(<span class='font_6'>" . htmlspecialchars($detail['part_color']) . "</span>)";
                          $unit = $detail['unit_value'];
                          $qty = $detail['total_qty'];
                          $price = $detail['price'];
                          $amount = ($qty*$price);
                          $vat_amount = $amount*$vat/100;
                          $amount_with_vat = ($vat_amount) ? ($amount + $vat_amount) : $amount;
                          $all_qty = $all_qty + $qty;
                          $all_amount = $all_amount + $amount;
                          $all_vat_amount = $all_vat_amount + $vat_amount;
                          $all_amount_with_vat = $all_amount_with_vat + $amount_with_vat;
                          $vat_txt = ($vat > 0) ? $vat + 0 : 'Без НДС';

                        ?>
                       <tr height="28" class="font_9 v_middle tr_border_thin">
                        <td class="txt_center p_rigtht3"><?=$tno?></td>
                        <td class="p_left3" colspan="2" style="white-space: normal;"><?=$ptno?></td>
                        <td class="txt_center font_7 txt_wrap" style="max-width:30px"><?=$unit?></td>
                        <td class="txt_right p_rigtht3"><?=str_replace('.00', '', number_format($qty, 2, '.', ' '));?></td>
                        <td class="txt_right p_rigtht3"><?=str_replace('.00', '', number_format($price, 2, '.', ' '));?></td>
                        <td class="txt_right p_rigtht3"><?=str_replace('.00', '', number_format($amount, 2, '.', ' '));?></td>
                        <? if($vat_txt === 'Без НДС') { ?>
                          <td colspan="2" class="txt_center"><?=$vat_txt;?></td>
                          <td class="txt_right p_rigtht3"><?=str_replace('.00', '', number_format($amount_with_vat, 2, '.', ' '));?></td>
                        <? } else { ?>
                          <td class="txt_right p_rigtht3"><?=str_replace('.00', '', number_format($vat, 2, '.', ' '));?></td>
                          <td class="txt_right p_rigtht3"><?=str_replace('.00', '', number_format($vat_amount, 2, '.', ' '));?></td>
                          <td class="txt_right p_rigtht3"><?=str_replace('.00', '', number_format($amount_with_vat, 2, '.', ' '));?></td>
                        <? } ?> 
                      </tr>
                      
                      <?php endforeach;?>
                    <?php endforeach;?>
                    <tr height="16" class="font_9 v_middle font_bold tr_border_thin">
                      <td colspan="3" class="border_thin txt_right">Итого:</td>
                      <td colspan="2" class="txt_right p_rigtht3"><?=str_replace('.00', '', number_format($all_qty, 2, '.', ' '));?></td>
                      <td colspan="2" class="txt_right p_rigtht3"><?=number_format($all_amount, 2, '.', ' ');?></td>
                      <td colspan="2" class="txt_right p_rigtht3"><?=($all_vat_amount == 0) ? '' : number_format($all_vat_amount, 2, '.', ' ');?></td>
                      <td class="txt_right p_rigtht3"><?=($all_amount_with_vat == 0) ? '' : number_format($all_amount_with_vat, 2, '.', ' ');?></td>
                    </tr>
                  <?php endif;?>

                
            </tbody>
          </table>
    
          <h2>Оплаты</h2>             
          <table class="inv_detail">
            <thead>
              <tr class="tr_border_thin font_bold txt_center tr_border_thin">
                <th class="font_13 th_cyanHeader txt_center" style="width: 10px;">#</th>
                <th class="font_13 th_cyanHeader txt_center" style="width: 30px;">Оплаты №</th>
                <th class="font_13 th_cyanHeader txt_center" style="width: 30px;">Способ оплаты</th>
                <th class="font_13 th_cyanHeader txt_center" style="width: 30px;">Дата</th>
                <th class="font_13 th_cyanHeader txt_center" style="width: 30px;">Количество</th>
              </tr>
            </thead>
            <?php if(isset($payments['receptControls'])):?>
              <tbody>
                <?php foreach($payments['receptControls'] as $key => $item):?>
                  <?php 
                    $recept_control_amount += $item['recept_control_amount'];
                  ?>
                  <tr height="16" class="font_9 v_middle  tr_border_thin">
                      <td class="font_9 txt_center" style="width: 10px;"  ><?= $key+1?></td>
                      <td class="font_9 txt_center" style="width: 30px;"  ><?= $item['recept_control_no']?></td>
                      <td class="font_9 txt_center" style="width: 30px;"  ><?= $item['payment_term']?></td>
                      <td class="font_9 txt_center" style="width: 30px;"  ><?= $item['recept_control_date']?></td>
                      <td class="font_9 txt_center" style="width: 30px;"  ><?= divideString($item['recept_control_amount']*1, 3)?></td>
                  </tr>
                <?php endforeach;?>
                <tr height="16" class="font_9 v_middle font_bold tr_border_thin">
                  <td colspan="4" class="font_13 txt_right" style="width: 30px;">Итого: </td>
                  <td class="font_13 txt_center " style="width: 30px;" ><?= divideString($recept_control_amount, 3)?></td>
                </tr>
              </tbody>
            <?php endif;?>
          </table>
          </br>
          </br>
          </br>
          <p class="font_bold txt_center font_13">№ ТТН: <?= implode(',',$result['invoice_no'])?></p>
  </div>