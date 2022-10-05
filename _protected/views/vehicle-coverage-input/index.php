<?php
use app\components\Helpers;
use app\models\VehicleCoverageInput;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/** @var TYPE_NAME $productModel */
/** @var TYPE_NAME $descriptionList */
/** @var TYPE_NAME $rowCurrent */
/** @var TYPE_NAME $rowPaid */
/** @var TYPE_NAME $rowETA */
$this->title = Yii::t('app', 'Vehicle set coverage input');
$this->params['breadcrumbs'][] = $this->title;
$canRefresh = Yii::$app->user->can('vehicle-coverage-input-refresh');
?>

<style>
  .tr_head th{
    font-size: 16px !important;
  }
  .fix_table tr th, .fix_table tr td{
    border-color: #f3f3f3 !important;
  }
  .tr_head th, .tr_head td{
    background-color: #e8ebf3;
    text-align: center;
  }
  .tr_total td {
    background-color: #bfc2c5;
  }

  .tr_head th, .tr_head td{
    border-bottom-width: 1px !important;
  }
  .mln{
		text-align: right;
		font-size: 12px;
		border: 0px !important;
	}
  .table-doh {
		border: 0px !important;
	}
      
</style>

<div class="row">
  <div class="col-xs-12">
    <div class="form-group pull-right">
      <?=Html::button(Yii::t('app', 'btn-download'), ['class' => 'btn btn-info btn-sm', 'id' => 'download-xls'])?>
    </div>
  </div>
</div>

<div class="row">
<div id="div_fix_table" style="margin: 10px 200px 0px 200px;">
                  <table id="fix_table" class="table table-bordered table-hover table-doh">
      <thead>
      <tr>
          <td colspan="<?=count($productModel)+1?>" class="mln"><?=Yii::t('app','Last updated date')?>: <b><?=VehicleCoverageInput::getLastCoverageDate()?></b></td>
        </tr>
      <tr class="tr_head">
        <th class="text-center w_desc" rowspan="2" style="width: 15%;"><?=yii::t('app', 'Description')?></th>
        <th class="text-center w_model" colspan="<?=count($productModel)?>">
            <?=yii::t('app', 'Model')?>
        </th>
      </tr>
      <tr class="tr_head">
        <? foreach ($productModel as $modelId => $modelName) { ?>
          <th class="w_model width_fillable" style="height:30px;">
            <? if ($canRefresh) { ?>
              <?
              echo Html::a(
                $modelName,
                Url::toRoute(['vehicle-coverage-input/refresh', 'modelId' => $modelId]),
                [
                  'class' => 'f_bold text-center',
                  'style' => 'text-decoration:none',
                  'title' => Yii::t('app', 'Refresh'),
                ]
              );
              ?>
            <? } ?>
          </th>
        <? } ?>
      </tr>

      <tr class="tr_head tr_total">
        <td class="text-center f_bold width_fillable w_date">
            <?=VehicleCoverageInput::getDescriptionText(VehicleCoverageInput::CURRENT_STOCK)?>
        </td>
        <?
        $qq = 1;
        foreach ($productModel as $modelKey => $modelName) {
          $qtyBor = false;
          foreach ($rowCurrent as $rowKey => $rowVal) {
            if ($modelKey == $rowKey) {
              $arrKey = array_keys($rowVal);
              $title = $arrKey[0];
              $arrVal = array_values($rowVal);
              $qty = $arrVal[0];
              $qtyBor = true;
              ?>
              <td title = "<?=$title?>" class="text-center f_bold  bg_DFF0D8 width_fillable">
                <?=Helpers::numberFormatRemoveZero($qty, 4, ".", "")?>
              </td>
              <?
            }
          }
          if ($qtyBor == false) {
            echo "<td></td>";
          }
        } ?>
      </tr>

      <tr class="tr_head tr_total">
        <td class="text-center f_bold width_fillable w_date">
            <?=VehicleCoverageInput::getDescriptionText(VehicleCoverageInput::UAM_STOCK)?>
        </td>
        <?
        $qq = 1;
        foreach ($productModel as $modelKey => $modelName) {
          $qtyBor = false;
          foreach ($rowUam as $rowKey => $rowVal) {
            if ($modelKey == $rowKey) {
              $arrKey = array_keys($rowVal);
              $title = $arrKey[0];
              $arrVal = array_values($rowVal);
              $qty = $arrVal[0];
              $qtyBor = true;
              ?>
              <td title = "<?=$title?>" class="text-center f_bold  bg_DFF0D8 width_fillable">
                <?=Helpers::numberFormatRemoveZero($qty, 4, ".", "")?>
              </td>
              <?
            }
          }
          if ($qtyBor == false) {
            echo "<td></td>";
          }
        } ?>
      </tr>

      <tr class="tr_head tr_total">
        <td class="text-center f_bold width_fillable w_date">
            <?=VehicleCoverageInput::getDescriptionText(VehicleCoverageInput::INTRANSIT_ETA)?>
        </td>
        <td colspan="<?=count($productModel)?>"></td>
      </tr>
      </thead>
      <tbody>
      <?
      $cnt = 1;
      foreach ($rowETA as $rowDateKey => $rowDateVal) { ?>
        <tr>
          <td class="text-center w_date <?if($rowDateKey < date('Y-m-d')) echo 'text-danger text-bold'?>"><?=$rowDateKey?></td>
          <?
          $cnt++;
          $qq = 1;
          foreach ($productModel as $modelKey => $modelName) {
            $qtyBor = false;
            foreach ($rowDateVal as $rowKey => $rowVal) {
              if ($modelKey == $rowKey) {
                $qtyBor = true;
                ?>
                <td class="text-center f_bold  bg_DFF0D8 width_fillable">
                  <?=Helpers::numberFormatRemoveZero($rowVal, 4, ".", "");?>
                </td>
                <?
              }
            }
            if ($qtyBor == false) {
              echo "<td></td>";
            }
          } ?>
        </tr>
      <? } ?>

      </tbody>
      <tfoot>
      <tr class="tr_total">
        <td class="text-center f_bold width_fillable w_date">
            <?=VehicleCoverageInput::getDescriptionText(VehicleCoverageInput::PAID_NOT_SHIPPED_ORDER)?>
        </td>
        <?
        $qq = 1;
        foreach ($productModel as $modelKey => $modelName) {
          $qtyBor = false;
          foreach ($rowPaid as $rowKey => $rowVal) {
            if ($modelKey == $rowKey) {
              $arrKey = array_keys($rowVal);
              $title = $arrKey[0];
              $arrVal = array_values($rowVal);
              $qty = $arrVal[0];
              $qtyBor = true;
              ?>
              <td title="<?=$title?>" class="text-center f_bold  bg_DFF0D8 width_fillable">
                <?=Helpers::numberFormatRemoveZero($qty, 4, ".", "")?>
              </td>
              <?
            }
          }
          if ($qtyBor == false) {
            echo "<td></td>";
          }
        } ?>
      </tr>
      </tfoot>
    </table>
  </div>
</div>
<?
$scirpt = <<< JS
$(document).ready(function() {
	
	$('#fix_table').tableFixer({
	  'left' : 0,
	  'foot' : true,
	  'head' : true
	});
	
	changeHeight();
  
	$(window).resize(function(){
		changeHeight();
  });
  
	function changeHeight(){
    window_h = $(window).height();
    table_h = window_h - 220;
    // console.log(window_h+"-"+table_h);
    $('#div_fix_table').height(table_h+'px');
  }

  $('#download-xls').on('click', function (e) {
      html_xls_export('fix_table', '$downloadFileName');
  });    
});
JS;
$this->registerJs($scirpt, yii\web\View::POS_END);
?>
