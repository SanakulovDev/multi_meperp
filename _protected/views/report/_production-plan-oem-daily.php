<?php
use kartik\datetime\DateTimePicker;
use yii\helpers\Html;

/** @var TYPE_NAME $need_monthOEM */
/** @var TYPE_NAME $oemDaily */
/** @var TYPE_NAME $oemDailyData */
$this->title = Yii::t('app', 'OEM Daily');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
  <div class="col-xs-6 col-sm-4 col-md-2  form-group-sm">
    <?=DateTimePicker::widget(
      [
        'name' => 'need_monthOEM',
        'value' => $need_monthOEM,
        'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
        'layout' => '{picker}{input}{remove}',
        'removeButton' => ['position' => 'append'],
        'language' => 'ru',
        'pluginOptions' => [
          'autoclose' => true,
          'format' => 'yyyy-mm',
          'startView' => 'year',
          'minView' => 'year',
          'maxView' => 'year',
        ],
        'options' => [
          'autocomplete' => 'off',
          'placeholder' => Yii::t('app', 'need_monthOEM'),
          'class' => ' form-control'
        ]
      ]
    );?>
  </div>
  <div class="col-xs-2 col-sm-2 col-md-2">
    <?=Html::submitButton(Yii::t('app', 'btn-show'), ['class' => 'btn btn-primary btn-sm'])?>
  </div>

  <div class="col-xs-4 col-sm-6 col-md-8">
    <div class="form-group pull-right">
      <?=Html::button(Yii::t('app', 'btn-download-delivery-plan'), ['class' => 'btn btn-info btn-sm', 'id' => 'downloadXlsOemDaily'])?>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-12 col-lg-12">
    <div class="div_fix_table_oem col-md-12  col-lg-12">
      <table id="fix_table_oem_daily" class="table table-striped table-bordered table-condensed table-sm-padding_2_0">
        <thead>
        <tr>
          <th><?=Yii::t('app', 'Model')?></th>
          <?
          $monthEndDate = date('t', strtotime(date("Y-m-t", strtotime($need_monthOEM))));
          $amtOy = [];
          for ($i = 1; $i <= $monthEndDate; $i++) {
            $amtOy[$i] = 0;
            ?>
            <th class="text-center"> <?=sprintf("%02d", $i)?> </th>
          <? } ?>
          <th class="text-center f_bold"><?=Yii::t('app', 'Total')?></th>
        </tr>
        </thead>
        <tbody>

        <?
        $allAmt = 0;
        foreach ($oemDailyData as $oemDailyKey => $prodList) {
          ?>
          <tr>
            <td class="midtext td-nowrap"><?=$oemDailyKey?></td>
            <?
            $amt = 0;
            for ($i = 1; $i <= $monthEndDate; $i++) {
              $bor = 0;
              foreach ($prodList as $index => $itemQty) {
                if (sprintf("%02d", $i) == substr($index, "-2")) {
                  $amtOy[$i] += $itemQty;
                  $amt += $itemQty;
                  $bor = 1;
                  ?>
                  <td class="midtext text-right"><?=$itemQty?></td>
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
            <td class="midtext text-right" style="font-weight:bold"> <?=$amt;?> </td>
          </tr>
        <? } ?>
        </tbody>
        <tfoot>
        <tr>
          <th class="text-right f_bold"><?=Yii::t('app', 'Total')?></th>
          <? for ($i = 1; $i <= $monthEndDate; $i++) { ?>
            <th class="text-right f_bold"><?=$amtOy[$i]?></th>
          <? } ?>
          <th class="midtext text-right" style="font-weight:bold"><?=$allAmt;?></th>
        </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>
