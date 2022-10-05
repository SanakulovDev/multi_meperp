<?php
use app\models\Factory;
use app\models\Lms;
use yii\helpers\Url;

/** @var TYPE_NAME $sequence */
/** @var TYPE_NAME $invNo */
/** @var TYPE_NAME $invDT */
/** @var TYPE_NAME $qty */
/** @var TYPE_NAME $unit */
/** @var TYPE_NAME $editedLastSeq */
/** @var TYPE_NAME $copies */
?>
<?
$labelCrtDT = date('Ymd H:i');
$supplierName = $model->supplier ? ($model->supplier->duns."/".$model->supplier->name) : '';
$ptno = $model->part->part_no;
$ptnm = $model->part->part_name;
$ptSide = $model->part->side;
$ptno4 = substr($ptno, -4);
$dloc = (Lms::find()->where(['part_id' => $model->part->id])->one()->dloc) ?? '-';
$factory = Factory::find()->where(['is_main' => 1])->one() ?? null;
$duns = $factory->duns ?? null;
$alias = $factory->alias ?? null;
$dunsClient = (strlen(trim($duns.$alias)) > 0) ? $duns."/".$alias : '-';
?>

<?
for($sequence = $editedLastSeq - $copies + 1; $sequence <= $editedLastSeq; $sequence++) {
  $sequence = str_pad($sequence, 6, 0, STR_PAD_LEFT);
  $serialNo = "P:".$ptno.":".$qty.":".$sequence.":".date('ymd');
  ?>
  <div class="qrcode_div08">
    <table class="tbl_qrcode" align="center">
      <tr>
        <td colspan="4">
          <div class="pull-right txt_right f_bold pr-3"> <?=Yii::$app->params['comp_name'];?> </div>
          <div><img class="h25 pl-3" src="<?=Url::home();?>img/logo.jpg"></div>
        </td>
      </tr>
      <tr>
        <td colspan="3" class="bottom_border_none f10 pl-3 lh08">Деталь коди:</td>
        <td rowspan="2" class="f45 txt_center f_bold lh08"><?=$ptno4;?></td>
      </tr>
      <tr>
        <td colspan="3" class="top_border_none f20 txt_center f_bold lh08"><?=$ptno?></td>
      </tr>
      <tr>
        <td colspan="4" class="bottom_border_none f10 pl-3 lh08">Деталь номи:</td>
      </tr>
      <tr>
        <td colspan="4" class="top_border_none txt_center f_bold lh08
						<? if(strlen($ptnm) > 40) {
          echo "f12";
        } else {
          echo "f13";
        } ?>
			">
          <?=$ptnm;?>
        </td>
      </tr>
      <tr>
        <td colspan="2" class="bottom_border_none f10 pl-3 lh08">Инвойс номери:</td>
        <td colspan="2" class="bottom_border_none f10 pl-3 lh08">Инвойс санаси:</td>
      </tr>
      <tr>
        <td colspan="2" class="qr_invNo top_border_none f14 txt_center f_bold lh08"><?=($invNo) ?? '-'?></td>
        <td colspan="2" class="qr_invDT top_border_none f14 txt_center f_bold lh08"><?=($invDT) ?? '-'?></td>
      </tr>
      <tr>
        <td colspan="4" class="bottom_border_none f10 pl-3 lh08">DUNS (ИНН)/Поставщик:</td>
      </tr>
      <tr>
        <td colspan="4" class="top_border_none f14 txt_center f_bold lh08">
          <?=$supplierName?>
        </td>
      </tr>
      <tr>
        <td class="bottom_border_none f10 pl-3 lh08">DUNS (ИНН)/Клиент:</td>
        <td rowspan="8" colspan="3" class="txt_center lh08 qrCodeImg">
        </td>
      </tr>
      <tr>
        <td class="top_border_none f14 txt_center f_bold lh08 pl-3 pr-3">
          <?=$dunsClient?>
        </td>
      </tr>
      <tr>
        <td class="bottom_border_none f10 pl-3 lh08">Деталь сони(<?=$unit?>):</td>
      </tr>
      <tr>
        <td class="qr_qty top_border_none f35 txt_center f_bold lh08"><?=$qty?></td>
      </tr>
      <tr>
        <td class="bottom_border_none f10 pl-3 lh08">DLOC:</td>
      </tr>
      <tr>
        <td class="top_border_none f18 txt_center f_bold lh08"><?=$dloc?></td>
      </tr>
      <tr>
        <td class="bottom_border_none f10 pl-3 lh08">Томони:</td>
      </tr>
      <tr>
        <td class="top_border_none f18 txt_center f_bold lh08">
          <?=$ptSide?>
        </td>
      </tr>
      <tr class="serialNo">
        <td colspan="4" class="f10 txt_center lh08"><?=$serialNo?></td>
      </tr>
      <tr>
        <td colspan="4" class="f10 pl-3 lh08">
          <div class="pull-right txt_right pr-3 lh08">
            <?=$labelCrtDT?>
          </div>
          <div> <?=Yii::$app->user->identity->fullname?> </div>
        </td>
      </tr>
    </table>
  </div>
  <div class='break_page'></div>
<? } ?>