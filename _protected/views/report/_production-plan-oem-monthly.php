<?php
use app\components\Helpers;
use yii\helpers\Html;

/** @var TYPE_NAME $oemMonth */
/** @var TYPE_NAME $oemMonthlyData */
$this->title = Yii::t('app', 'OEM Monthly');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
  <div class="col-xs-6 col-sm-4 col-md-2  form-group-sm">
    <?
    for ($j = -5; $j < 6; $j++) {
      $yyyy = date("Y", strtotime(($j." year"), time()));
      $oemMonthlyList[$yyyy] = $yyyy;
    }
    $params = ['class' => 'form-control'];
    echo Html::dropDownList('oemMonth', $oemMonth, $oemMonthlyList, $params);
    ?>
  </div>
  <div class="col-xs-2 col-sm-2 col-md-2">
    <?=Html::submitButton(Yii::t('app', 'btn-show'), ['class' => 'btn btn-primary btn-sm'])?>
  </div>
  <div class="col-xs-4 col-sm-6 col-md-8">
    <div class="form-group pull-right">
      <?=Html::button(Yii::t('app', 'btn-download-delivery-plan'), ['class' => 'btn btn-info btn-sm', 'id' => 'downloadXlsOemMonthly'])?>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12 col-lg-12">
    <div class="div_fix_table_oem col-md-12  col-lg-12">
      <table id="fix_table_oem_monthly" class="table table-striped table-bordered table-condensed table-sm-padding_2_0">
        <thead>
        <tr>
          <th><?=Yii::t('app', 'Model')?></th>
          <?
          for ($i = 1; $i <= 12; $i++) {
            $amtOy[$i] = 0;
            ?>
            <th class="text-center"> <?=Yii::t('app', date("F", strtotime("2020-".$i."-01")))?> </th>
          <? } ?>
          <th class="text-center f_bold"><?=Yii::t('app', 'Total')?></th>
        </tr>
        </thead>
        <tbody>

        <?
        $allAmt = 0;
        foreach ($oemMonthlyData as $oemMonthlyKey => $prodList) {
          ?>
          <tr>
            <td class="midtext td-nowrap"><?=$oemMonthlyKey?></td>
            <?
            $amt = 0;
            for ($i = 1; $i <= 12; $i++) {
              $bor = 0;
              foreach ($prodList as $index => $itemQty) {
                if (sprintf("%02d", $i) == substr($index, "-2")) {
                  $amtOy[$i] += $itemQty;
                  $amt += $itemQty;
                  $bor = 1;
                  ?>
                  <td class="midtext text-right"><?=Helpers::numberFormatRemoveZero($itemQty)?></td>
                  <? break; ?>
                  <?
                }
              }
              if ($bor == 0) {
                ?>
                <td></td><?
              }
            }
            $allAmt += $amt;
            ?>
            <td class="midtext text-right" style="font-weight:bold"> <?=Helpers::numberFormatRemoveZero($amt);?> </td>
          </tr>
        <? } ?>
        </tbody>
        <tfoot>
        <tr>
          <th class="text-right f_bold"><?=Yii::t('app', 'Total')?></th>
          <? for ($i = 1; $i <= 12; $i++) { ?>
            <th class="text-right f_bold"><?=Helpers::numberFormatRemoveZero($amtOy[$i])?></th>
          <? } ?>
          <th class="midtext text-right" style="font-weight:bold"><?=Helpers::numberFormatRemoveZero($allAmt);?></th>
        </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>
