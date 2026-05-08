<?php
use app\assets\AdminLteAsset;
use yii\helpers\Html;
use yii\web\YiiAsset;

/* @var $this yii\web\View */
/* @var $model app\models\FgInvoice */
$this->title = $model->invoice_no."(".$model->invoice_date.")";
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'FG Invoice'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="fg-invoice-print">
  <?=Html::a(Yii::t('app', 'btn-back'), ['index'], ['class' => 'btn btn-primary btn-sm'])?>
  <?
  $this->registerCssFile("@themes/css/fg_invoice_print.css", ['depends' => [AdminLteAsset::className()]]);
  ?>
  <div class="clearfix"></div>
  <div class="row" align="center" id="print_div">
    <table class="inv_detail">
      <colgroup>
        <col class="xl65345" width="35">
        <col class="xl65345" width="300">
        <col class="xl65345" width="30">
        <col class="xl65345" width="auto">
        <col class="xl65345" width="auto">
        <col class="xl65345" width="auto">
        <col class="xl65345" width="auto">
        <col class="xl65345" width="auto">
        <col class="xl65345" width="auto">
      </colgroup>
      <tr height="22">
        <td colspan="9" class="font_13 txt_center font_bold v_top">
          Товарно-транспортная накладная № <?=$model->invoice_no;?> от <?=date("d.m.Y", strtotime($model->invoice_date));?> г.
        </td>
      </tr>
      <tr height="30">
        <td colspan="9" class="font_10 txt_center font_bold v_top">
          к договору: <?=$model->contract?> от <?=$model->contract_date?>
        </td>
      </tr>
      <tr height="37">
        <td colspan="4" height="37" class="font_9 txt_left v_top">
          Поставщик: <span class="font_9 font_underline font_bold"><?=$model->factory->name?></span>
        </td>
        <td></td>
        <td colspan="4" class="font_9 txt_left v_top">
          Покупатель:<span class="font_9 font_underline font_bold"><?=$model->customer->name?></span>
        </td>
      </tr>
      <tr height="55">
        <td colspan="4" class="adress_rekvizit">
          Адрес:<span class="font_10 font_underline font_bold"><?=$model->factory->address?></span>
        </td>
        <td></td>
        <td colspan="4" class="adress_rekvizit">
          Адрес:<span class="font_10 font_underline font_bold"><?=$model->customer->address?></span>
        </td>
      </tr>
      <tr height="37">
        <td colspan="4" class="adress_rekvizit">
          <span class="font_8">Идентификационный номер <br>	поставщика(ИНН): </span>
          <span class="font_10 font_underline font_bold"><?=$model->factory->tin?></span>
        </td>
        <td></td>
        <td colspan="4" class="adress_rekvizit">
          <span class="font_8">Идентификационный номер <br>покупателя(ИНН): </span>
          <span class="font_10 font_underline font_bold"><?=$model->customer->tin?></span>
        </td>
      </tr>
      <tr height="45">
        <td colspan="4" class="adress_rekvizit">Регистрационный код плательщика<br>НДС:
          <span class="font_10 font_underline font_bold"><?=$model->factory->vat?></span>
        </td>
        <td></td>
        <td colspan="4" class="adress_rekvizit">Регистрационный код плательщика<br> НДС:
          <span class="font_10 font_underline font_bold"><?=$model->customer->vat?></span>
        </td>
      </tr>
      <tr height="9">
        <td colspan="4" class="font_9">
          <?
          $remark_txt = ($model->factory->remark) ? " (".$model->factory->remark.")" : '';
          ?>
          DUNS:<span class="font_underline font_bold"><?=$model->factory->duns?></span><?=$remark_txt?>
        </td>
        <td colspan="5"></td>
      </tr>
      <tr height="20" class="tr_border_thin font_bold txt_center">
        <td rowspan="2" class="font_8 th_cyanHeader">№</td>
        <td rowspan="2" colspan="4" class="font_8 th_cyanHeader">Наим.товаров<br>(работ, услуг)</td>
        <td rowspan="2" class="font_8 th_cyanHeader">Ед.<br>изм</td>
        <td rowspan="2" class="font_8 th_cyanHeader">Цена</td>
        <td rowspan="2" class="font_8 th_cyanHeader">Кол-во</td>
        <!--				<td rowspan="2" class="font_8 th_cyanHeader">Стоимость<br>поставки</td>-->
        <!--				<td colspan="2" class="font_8 th_cyanHeader">НДС</td>-->
        <!--				<td rowspan="2" class="font_8 th_cyanHeader">Стоимость<br>поставки<br>с учетом НДС</td>-->
      </tr>
      <tr height="31" class="tr_border_thin font_bold txt_center">
        <!--				<td class="font_8 th_cyanHeader">Став-ка</td>-->
        <!--				<td class="font_8 th_cyanHeader">Сумма</td>-->
      </tr>
      <?
      if(isset($model->fgInvoiceDetails)) {
        $details = $model->fgInvoiceDetails;
        $tno = 0;
        $all_qty = 0;
        $all_amount = 0;
        $all_vat_amount = 0;
        $all_amount_with_vat = 0;
        foreach($details as $index => $detail) {
          $tno++;
          $vat = $model->vat;
          $ptno = "Полимерный компаунд ".$detail->part_name."(<span class='font_7'>".$detail->part->part_color."<span>)";
          $unit = $detail->unit->unit_value;
          $qty = $detail->qty;
          $price = $detail->price;
          $amount = ($qty*$price);
          $vat_amount = $amount*$vat/100;
          $amount_with_vat = ($vat_amount) ? ($amount + $vat_amount) : 0;
          $all_qty = $all_qty + $qty;
          $all_amount = $all_amount + $amount;
          $all_vat_amount = $all_vat_amount + $vat_amount;
          $all_amount_with_vat = $all_amount_with_vat + $amount_with_vat;
          $vat_txt = ($vat > 0) ? $vat + 0 : 'Без НДС';
          ?>
          <tr height="28" class="font_9 v_middle tr_border_thin">
            <td class="txt_right p_rigtht3"><?=$tno?></td>
            <td colspan="4" class="txt_wrap p_left3"><?=$ptno?></td>
            <td class="txt_center font_7 txt_wrap"><?=$unit?></td>
            <td class="txt_right p_rigtht3"><?=round($price, 2)?></td>
            <td class="txt_right p_rigtht3"><?=round($qty, 2)?></td>
    
            ?>
          </tr>
        <? } ?>
        <tr height="16" class="font_9 v_middle font_bold tr_border_thin">
          <td colspan="7" class="border_thin txt_right">Итого: &nbsp;</td>
          <td class="txt_right p_rigtht3"><?=round($all_qty, 2)?></td>
          <!--						<td class="txt_right p_rigtht3"></td>-->
          <!--						<td class="txt_right p_rigtht3">--><?//=round($all_amount,2);
          ?><!--</td>-->
          <!--						<td colspan="2" class="txt_right p_rigtht3">--><?//=($all_vat_amount == 0) ? '' : round($all_vat_amount,2)
          ?><!--</td>-->
          <!--						<td class="txt_right p_rigtht3">--><?//=($all_amount_with_vat == 0) ? '' : round($all_amount_with_vat,2)
          ?><!--</td>-->
        </tr>
      <? }
      ?>
    </table>
  </div>
</div>
