<?php
use app\assets\AdminLteAsset;
use app\components\Helpers;
use yii\helpers\Html;
use yii\web\JqueryAsset;
use yii\web\YiiAsset;

/* @var $this yii\web\View */
/* @var $model app\models\Waybill */
/* @var $details */
$this->title = Yii::t('app', 'Invoice'). ": ". $model->waybill_no."(".$model->waybill_date.")";
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Waybill'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>

<div class="fg-invoice-print">
  <p class="pull-right">
    <?=Html::a(Yii::t('app', 'btn-back'), ['index'], ['class' => 'btn btn-success btn-sm pull-right'])?>
    <?=Html::button(Yii::t('app', 'btn-print'), ['class' => 'btn btn-info btn-sm pull-right margin-r-5', 'id' => 'WaybillPrint'])?>
  </p>
  <?
  $this->registerCssFile("@themes/css/fg_invoice_print.css", ['depends' => [AdminLteAsset::className()]]);
  $this->registerJsFile("@themes/js/printThis.js", ['depends' => [JqueryAsset::className()]]);
  ?>
  <?
  foreach($model->fgInvoiceWaybills as $invWaybill) {
    $fgInvoices[] = $invWaybill->fgInvoice;
  }
  $firstFgInvoice = count($fgInvoices) > 0 ? $fgInvoices[0] : '';
  $customers[] = $fgInvoices[0]->customer;
  $firstCustomer = count($customers) > 0 ? $customers[0] : '';
  ?>
  <div class="clearfix"></div>
  <div class="row" id="print_div" align="center" style=" margin-right:5px">
    <table class="inv_detail">
      <tr height="22">
        <td colspan="9" class="font_13 txt_center font_bold v_top">
          Счет-фактура № <?=$model->waybill_no;?> от <?=date("d.m.Y", strtotime($model->waybill_date));?> г.
        </td>
      </tr>
      <tr height="30">
        <td colspan="9" class="font_10 txt_center font_bold v_top">
          к договору: <?=$firstFgInvoice->contract?> от <?=$firstFgInvoice->contract_date?>
        </td>
      </tr>
      <tr height="37">
        <td colspan="4" height="37" class="font_9 txt_left v_top">
          Поставщик: <span class="font_9 font_underline font_bold"><?=$model->factory->name?></span>
        </td>
        <td></td>
        <td colspan="4" class="font_9 txt_left v_top">
          Покупатель:<span class="font_9 font_underline font_bold">
            <?=$firstCustomer->name?>
          </span>
        </td>
      </tr>
      <tr height="55">
        <td colspan="4" class="adress_rekvizit">
          Адрес:<span class="font_10 font_underline font_bold"><?=$model->factory->address?></span>
        </td>
        <td></td>
        <td colspan="4" class="adress_rekvizit">
          Адрес:<span class="font_10 font_underline font_bold">
            <?=$firstCustomer->address;?>
          </span>
        </td>
      </tr>
      <tr height="37">
        <td colspan="4" class="adress_rekvizit">
          <span class="font_8">ИНН поставщика: </span>
          <span class="font_10 font_underline font_bold"><?=$model->factory->tin?></span>
        </td>
        <td></td>
        <td colspan="4" class="adress_rekvizit">
          <span class="font_8">ИНН покупателя: </span>
          <span class="font_10 font_underline font_bold"><?=$firstCustomer->tin?></span>
        </td>
      </tr>
      <tr height="45">
        <td colspan="4" class="adress_rekvizit">Регистрационный код плательщика<br>НДС:
          <span class="font_10 font_underline font_bold"><?=$model->factory->vat?></span>
        </td>
        <td></td>
        <td colspan="4" class="adress_rekvizit">Регистрационный код плательщика<br> НДС:
          <span class="font_10 font_underline font_bold"><?=$firstCustomer->vat?></span>
        </td>
      </tr>
      <tr height="9">
        <td colspan="4" class="font_9">
          <?
          $remark_txt = ($model->factory->remark) ? " (".$model->factory->remark.")" : '';
          ?>
          DUNS:<span class="font_underline font_bold"><?=$model->factory->duns?></span><?=$remark_txt?>
        </td>
        <td></td>
        <td colspan="4"></td>
      </tr>
    </table >

    <table class="inv_detail">
      <tr height="20" class="tr_border_thin font_bold txt_center">
        <td rowspan="2" class="font_8 th_cyanHeader" style="width: 30px;">№</td>
        <td rowspan="2" colspan="2" class="font_8 th_cyanHeader">Наим.товаров<br>(работ, услуг)</td>
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
      <?
      if(isset($details)){
      $tno = 0;
      $all_qty = 0;
      $all_amount = 0;
      $all_vat_amount = 0;
      $all_amount_with_vat = 0;
      $pg_num = 1;
      $pg_row_cnt = 0;
      foreach($details as $index => $detail){
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
      switch($pg_num) {
      case 1:
      if($tno >= 30){
      $pg_num++;
      $pg_row_cnt = 0;
      ?>
    </table>
    <div class="page_break_before"></div>
    <table class="inv_detail">
      <tr height="20" class="pbb tr_border_thin font_bold txt_center">
        <td rowspan="2" class="font_8" style="width: 30px;">№</td>
        <td rowspan="2" colspan="2" class="font_8">Наим.товаров<br>(работ, услуг)</td>
        <td rowspan="2" class="font_8" style="width: 40px;">Ед.<br>изм</td>
        <td rowspan="2" class="font_8" style="width: 60px;">Кол-во</td>
        <td rowspan="2" class="font_8" style="width: 70px;">Цена</td>
        <td rowspan="2" class="font_8" style="width: 70px;">Стоимость<br>поставки</td>
        <td colspan="2" class="font_8" style="width: 120px;">НДС</td>
        <td rowspan="2" class="font_8" style="width: 100px;">Стоимость<br>поставки с<br>учетом НДС</td>
      </tr>
      <tr height="31" class="pbb tr_border_thin font_bold txt_center">
        <td class="font_8" style="width: 30%;">Став-<br>ка %</td>
        <td class="font_8" style="width: 70%;">Сумма</td>
      </tr>
      <tr class="pbb font_8 txt_center v_middle font_bold tr_border_thin">
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
      <?
      }
      break;
      default:
      if($pg_row_cnt == 39){
      $pg_num++;
      $pg_row_cnt = 0;
      ?>
    </table>
    <div class="page_break_before"></div>
    <table class="inv_detail">
      <tr height="20" class="pbb tr_border_thin font_bold txt_center">
        <td rowspan="2" class="font_8" style="width: 30px;">№</td>
        <td rowspan="2" colspan="2" class="font_8">Наим.товаров<br>(работ, услуг)</td>
        <td rowspan="2" class="font_8" style="width: 40px;">Ед.<br>изм</td>
        <td rowspan="2" class="font_8" style="width: 60px;">Кол-во</td>
        <td rowspan="2" class="font_8" style="width: 70px;">Цена</td>
        <td rowspan="2" class="font_8" style="width: 70px;">Стоимость<br>поставки</td>
        <td colspan="2" class="font_8" style="width: 120px;">НДС</td>
        <td rowspan="2" class="font_8" style="width: 100px;">Стоимость<br>поставки с<br>учетом НДС</td>
      </tr>
      <tr height="31" class="pbb tr_border_thin font_bold txt_center">
        <td class="font_8" style="width: 30%;">Став-<br>ка %</td>
        <td class="font_8" style="width: 70%;">Сумма</td>
      </tr>
      <tr class="pbb font_8 txt_center v_middle font_bold tr_border_thin">
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
      <?
      }
      }
      $pg_row_cnt++;
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

      <? } ?>


      <tr height="16" class="font_9 v_middle font_bold tr_border_thin">
        <td colspan="3" class="border_thin txt_right">Итого:</td>
        <td colspan="2" class="txt_right p_rigtht3"><?=str_replace('.00', '', number_format($all_qty, 2, '.', ' '));?></td>
        <td colspan="2" class="txt_right p_rigtht3"><?=number_format($all_amount, 2, '.', ' ');?></td>
        <td colspan="2" class="txt_right p_rigtht3"><?=($all_vat_amount == 0) ? '' : number_format($all_vat_amount, 2, '.', ' ');?></td>
        <td class="txt_right p_rigtht3"><?=($all_amount_with_vat == 0) ? '' : number_format($all_amount_with_vat, 2, '.', ' ');?></td>
      </tr>

      <? }
      ?>
      <tr height="19">
        <td colspan="9"></td>
      </tr>
    </table>

    <table class="inv_detail">
      <tr height="19">
        <td colspan="9" class="font_8">
          Всего к оплате:
          <span class="font_bold">
						<? $sum2str_amt = ($all_amount_with_vat > 0) ? $all_amount_with_vat : $all_amount + $all_amount_with_vat; ?>
            <?=Helpers::mb_ucfirst(Helpers::sum2str_ru($sum2str_amt), "UTF-8", true);?>
					</span>
        </td>
      </tr>
      <tr height="40">
        <td colspan="5" class="font_10 v_bottom">
          <table style="width:100%">
            <tr>
              <td>Руководитель: <span class="font_bold"><?=$model->factory->head?></span></td>
              <td class="font_bold poluchil_imzo" style="width:100%"></td>
            </tr>
          </table>
        </td>
        <td class="font_8 font_bold txt_right v_bottom">Получил:</td>
        <td colspan="3" class="poluchil_imzo"></td>
      </tr>
      <tr height="40">
        <td colspan="5" class="font_10 v_bottom">
          <table style="width:100%">
            <tr>
              <td>Главный бухгалтер: <span class="font_bold"><?=$model->factory->chief_accountant?></span></td>
              <td class="font_bold poluchil_imzo" style="width:100%"></td>
            </tr>
          </table>
        </td>
        <td></td>
        <td colspan="3" class="poluchil_imzo"><? //$model->rec_person_regno?></td>
      </tr>
      <tr height="8">
        <td colspan="6"></td>
        <td colspan="3" class="imzo_izoh">Доверенность</td>
      </tr>
      <tr height="10">
        <td class="xl78345" colspan="2">М.П.(при наличиипечати)</td>
        <td colspan="7"></td>
      </tr>
      <tr height="40" style="height:30.0pt">
        <td colspan="5" class="font_10 v_bottom">
          <table style="width:100%">
            <tr>
              <td>Товар отпустил: <span class="font_bold"><? //$model->sender?></span></td>
              <td class="font_bold poluchil_imzo" style="width:100%"></td>
            </tr>
          </table>
        </td>
        <td></td>
        <td colspan="3" class="poluchil_imzo"><? //$model->rec_person_fullname?></td>
      </tr>
      <tr height="19">
        <td colspan="3" class="font_7 v_top txt_right"> (подпись ответственного лица от поставщика)</td>
        <td colspan="3"></td>
        <td colspan="3" class="imzo_izoh">ФИО получателя</td>
      </tr>
      <tr height="60">
        <td colspan="9">
          <b class="font_10">№ ТТН: </b>
          <span style="margin-left:10px" class="font_9">
            <?
            foreach($fgInvoices as $fgInvoice) {
              $fgInvoiceList[] = $fgInvoice->invoice_no;
            }
            echo implode(", ", $fgInvoiceList);
            ?>
          </span>
        </td>
      </tr>
    </table>
  </div>
  <?
  $print_scirpt = <<< JS
$('#WaybillPrint').on("click", function () {
  $('#print_div').printThis({
    // base: "window.location",
    base: false,                // preserve the BASE tag or accept a string for the URL
    loadCSS: ["/themes/adminlte/css/fg_invoice_print.css"],                // path to additional css file - use an array [] for multiple
    pageTitle: "",              // add title to print page
    debug: false,               // show the iframe for debugging
    importCSS: true,            // import parent page css
    importStyle: true,         // import style tags
    printContainer: true,       // print outer container/$.selector
    removeInline: false,        // remove inline styles from print elements
    removeInlineSelector: "*",  // custom selectors to filter inline styles. removeInline must be true
    printDelay: 333,              // variable print delay
    header: null,               // prefix to html
    footer: null,               // postfix to html
    formValues: true,           // preserve input/form values
    canvas: false,              // copy canvas content
    // doctypeString: null,        // enter a different doctype for older markup
    removeScripts: false,       // remove script tags from print content
    copyTagClasses: true,      // copy classes from the html & body tag
    beforePrintEvent: null,     // function for printEvent in iframe
    beforePrint: null,          // function called before iframe is filled
    afterPrint: null            // function called before iframe is removed
  });
});
JS;
  $this->registerJs($print_scirpt);
  ?>

