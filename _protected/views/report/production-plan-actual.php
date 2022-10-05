<?php
use app\assets\AdminLteAsset;
use app\components\Helpers;
use app\models\Part;
use app\models\ProductLine;
use app\models\Warehouse;
use kartik\datetime\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JqueryAsset;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DeliveryPlanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $need_date app\controllers\DeliveryPlanController */
/** @var TYPE_NAME $DB_part_list */
/** @var TYPE_NAME $warehouse_id */
/** @var TYPE_NAME $product_line_id */
/** @var TYPE_NAME $part_id */
/** @var TYPE_NAME $downloadFileName */
$this->title = Yii::t('app', 'Production target/actual');
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
  .not_modal{
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
    max-width:200px;
    outline:red dotted 1px;
    position:relative;
    cursor:help;
  }
  .comment_txt{display:none}
</style>
<div class="row">
  <?php $form = ActiveForm::begin(['action' => ['production-plan-actual'], 'method' => 'post',]); ?>
  <div class="col-md-2 col-lg-2">
    <? echo DateTimePicker::widget(
      [
        'name' => 'need_date',
        'value' => $need_date,
        'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
        'layout' => '{picker}{input}{remove}',
        'removeButton' => ['position' => 'append'],
        'language' => Yii::$app->language,
        'pluginOptions' => [
          'autoclose' => true,
          'format' => 'yyyy-mm-dd',
          'startView' => 'month',
          'minView' => 'month',
          'maxView' => 'month',
        ],
        'options' => [
          'autocomplete' => 'off',
          'placeholder' => Yii::t('app', 'Date'),
          'class' => ' form-control'
        ]
      ]
    ); ?>
  </div>
  <div class="col-md-2 col-lg-2">
    <?
    $model->warehouse_id = $warehouse_id;
    $warehouses = Warehouse::find()
                           ->select(['warehouse.id as id', 'warehouse.name as name'])
                           ->leftJoin('user_warehouse', 'warehouse.id = user_warehouse.warehouse_id')
                           ->where(['user_warehouse.user_id' => Yii::$app->user->id, 'warehouse_type' => 1]);
    //			echo "<pre>"; print_r($warehouses->createCommand()->rawSql);echo "</pre>";
    $warehouses = $warehouses->all();
    $warehouses_list = ArrayHelper::map($warehouses, 'id', 'name');
    $warehouses_list = [0 => Yii::t('app', 'Warehouse')] + $warehouses_list;
    $params = ['class' => 'form-control select2'];
    echo $form->field($model, 'warehouse_id')
              ->dropDownList($warehouses_list, $params)
              ->label(false);
    ?>
  </div>
  <div class="col-md-2 col-lg-2">
    <?
    $product_line = $product_line_id;
    $product_lines_list = ArrayHelper::map(ProductLine::find()->all(), 'id', 'linename');
    $product_lines_list = [0 => Yii::t('app', 'The production line')] + $product_lines_list;
    $params = ['class' => 'form-control select2'];
    echo Html::dropDownList('product_line_id', $product_line_id, $product_lines_list, $params);
    ?>
  </div>
  <div class="col-md-2 col-lg-2">
    <?
    $model->part_id = $part_id;
    $parts_list = ArrayHelper::map(Part::find()->all(), 'id', 'part_no');
    $parts_list = [0 => Yii::t('app', 'Part')] + $parts_list;
    $params = ['class' => 'form-control select2'];
    echo $form->field($model, 'part_id')
              ->dropDownList($parts_list, $params)
              ->label(false);
    ?>
  </div>
  <div class="col-md-2 col-lg-2">
    <?=Html::submitButton(Yii::t('app', 'btn-show'), ['class' => 'btn btn-primary'])?>
  </div>
  <?php ActiveForm::end(); ?>
  <div class="col-md-2 col-lg-2">
    <div class="form-group pull-right">
      <?=Html::button(Yii::t('app', 'btn-download-delivery-plan'), ['class' => 'btn btn-info', 'id' => 'download-xls'])?>
    </div>
  </div>
</div>
<br>
<div class="row">
  <?
  $partCount = (count($DB_part_list) > 0) ? true : false;
  $qq = 1;
  $b1_m = 0;
  $b2_m = 0;
  $b3_m = 0;
  $b4_m = 0;
  $b1_p = 0;
  $b2_p = 0;
  $b3_p = 0;
  $b4_p = 0;
  $b1_0 = 0;
  $b2_0 = 0;
  $b3_0 = 0;
  $b4_0 = 0;
  if ($partCount == true) { ?>
    <div class="col-md-12 col-lg-12">
      <div id="div_fix_table" class="col-md-12  col-lg-12">
        <table id="fix_table" class="table table-striped table-bordered table-condensed table-sm-padding_2_0">
          <thead>
          <tr>
            <th rowspan="4" class="txt_center"><?=Yii::t('app', 'Product no')?></th>
            <th rowspan="4" class="txt_center"><?=Yii::t('app', 'Line')?></th>
            <th colspan="15" class="txt_center"><?=$need_date?></th>
            <th colspan="5" rowspan="2" class="txt_center">
              <?=Yii::t('app', 'Month to date')." <br>(01.".date_format(date_create($need_date), "m.Y")."—".date_format(date_create($need_date), "d.m.Y").")"?>
            </th>
          </tr>
          <tr>
            <th colspan="5" class="txt_center">1-shift</th>
            <th colspan="5" class="txt_center">2-shift</th>
            <th colspan="5" class="txt_center">All</th>
          </tr>
          <tr>
            <? for ($r = 1;
            $r <= 4;
            $r++){ ?>
            <th rowspan="2"><?=Yii::t('app', 'plan')?></th>
            <th rowspan="2"><?=Yii::t('app', 'actual')?></th>
            <th colspan="3" class="b_<?=$r?>">
              <?=Yii::t('app', 'balance')?>
              <? } ?>
          </tr>
          <tr>
            <th class="txt_center mar bg-red" id="b1_m"></th>
            <th class="txt_center mar" id="b1_0"></th>
            <th class="txt_center mar bg-blue" id="b1_p"></th>
            <th class="txt_center mar bg-red" id="b2_m"></th>
            <th class="txt_center mar" id="b2_0"></th>
            <th class="txt_center mar bg-blue" id="b2_p"></th>
            <th class="txt_center mar bg-red" id="b3_m"></th>
            <th class="txt_center mar" id="b3_0"></th>
            <th class="txt_center mar bg-blue" id="b3_p"></th>
            <th class="txt_center mar bg-red" id="b4_m"></th>
            <th class="txt_center mar" id="b4_0"></th>
            <th class="txt_center mar bg-blue" id="b4_p"></th>
          </tr>
          </thead>
          <tbody>
          <?
          foreach ($DB_part_list as $prod_list) {
            $d_balance1 = '';
            $d_balance2 = '';
            $d_all_balance = '';
            $m_balance = '';
            if ($prod_list['d_plan1_qty'] != 0 || $prod_list['d_actual1_qty'] != 0) {
              $balance_sts = Helpers::dif_balance_sts($prod_list['d_plan1_qty'], $prod_list['d_actual1_qty']);
              switch ($balance_sts) {
                case (-1):
                  $d_balance1 = 'minus';
                  $b1_m++;
                  break;
                case (1):
                  $d_balance1 = 'plus';
                  $b1_p++;
                  break;
                default:
                  $b1_0++;
              }
            }
            if ($prod_list['d_plan2_qty'] != 0 || $prod_list['d_actual2_qty'] != 0) {
              $balance_sts = Helpers::dif_balance_sts($prod_list['d_plan2_qty'], $prod_list['d_actual2_qty']);
              switch ($balance_sts) {
                case (-1):
                  $d_balance2 = 'minus';
                  $b2_m++;
                  break;
                case (1):
                  $d_balance2 = 'plus';
                  $b2_p++;
                  break;
                default:
                  $b2_0++;
              }
            }
            if ($prod_list['d_all_plan'] != 0 || $prod_list['d_all_actual'] != 0) {
              $balance_sts = Helpers::dif_balance_sts($prod_list['d_all_plan'], $prod_list['d_all_actual']);
              switch ($balance_sts) {
                case (-1):
                  $d_all_balance = 'minus';
                  $b3_m++;
                  break;
                case (1):
                  $d_all_balance = 'plus';
                  $b3_p++;
                  break;
                default:
                  $b3_0++;
              }
            }
            if ($prod_list['m_plan_qty'] != 0 || $prod_list['m_actual_qty'] != 0) {
              $balance_sts = Helpers::dif_balance_sts($prod_list['m_plan_qty'], $prod_list['m_actual_qty']);
              switch ($balance_sts) {
                case (-1):
                  $m_balance = 'minus';
                  $b4_m++;
                  break;
                case (1):
                  $m_balance = 'plus';
                  $b4_p++;
                  break;
                default:
                  $b4_0++;
              }
            }
            ?>
            <tr>

              <td class="midtext">
                <?=$prod_list['part_no']?> (<span style='font-size:70%'><?=$prod_list['part_name']?></span>)
              </td>
              <td class="midtext"><?=$prod_list['warehouse_nm']?></td>
              <td
                <? if (strlen($prod_list['d_plan1_comment']) > 0) {
                  echo 'class="midtext txt_center not_modal"';
                } else {
                  echo 'class="midtext txt_center"';
                }
                ?>
              >
                <? if (strlen($prod_list['d_plan1_comment']) > 0) {
                  echo '<div class="comment_txt">'.$prod_list['d_plan1_comment'].'</div>';
                } ?>
                <?=($prod_list['d_plan1_qty'] != '-') ? Helpers::formatRemoveDecimal($prod_list['d_plan1_qty']) : '-'?>
              </td>


              <td class="midtext txt_center"> <?=($prod_list['d_actual1_qty'] != '-') ? Helpers::formatRemoveDecimal($prod_list['d_actual1_qty']) : '-'?></td>
              <td colspan="3" class="midtext txt_center f_bold b_1 <?=$d_balance1?>">
                <?=($prod_list['d_balance1'] != '-') ? Helpers::formatRemoveDecimal($prod_list['d_balance1']) : '-'?>
              </td>
              <td
                <? if (strlen($prod_list['d_plan2_comment']) > 0) {
                  echo 'class="midtext txt_center not_modal"';
                } else {
                  echo 'class="midtext txt_center"';
                }
                ?>
              >
                <? if (strlen($prod_list['d_plan2_comment']) > 0) {
                  echo '<div class="comment_txt">'.$prod_list['d_plan2_comment'].'</div>';
                } ?>
                <?=($prod_list['d_plan2_qty'] != '-') ? Helpers::formatRemoveDecimal($prod_list['d_plan2_qty']) : '-'?>
              </td>
              <td class="midtext txt_center"><?=($prod_list['d_actual2_qty'] != '-') ? Helpers::formatRemoveDecimal($prod_list['d_actual2_qty']) : '-'?></td>


              <td colspan="3" class="midtext txt_center f_bold b_2 <?=$d_balance2?>"> <?=($prod_list['d_balance2'] != '-') ? Helpers::formatRemoveDecimal($prod_list['d_balance2']) : '-'?></td>

              <td class="midtext txt_center"> <?=($prod_list['d_all_plan'] != '-') ? Helpers::formatRemoveDecimal($prod_list['d_all_plan']) : '-'?></td>
              <td class="midtext txt_center"> <?=($prod_list['d_all_actual'] != '-') ? Helpers::formatRemoveDecimal($prod_list['d_all_actual']) : '-'?></td>
              <td colspan="3" class="midtext txt_center f_bold b_3 <?=$d_all_balance?>"><?=($prod_list['d_all_balance'] != '-') ? Helpers::formatRemoveDecimal($prod_list['d_all_balance']) : '-'?></td>

              <td class="midtext txt_center"> <?=($prod_list['m_plan_qty'] != '-') ? Helpers::formatRemoveDecimal($prod_list['m_plan_qty']) : '-'?></td>
              <td class="midtext txt_center"> <?=($prod_list['m_actual_qty'] != '-') ? Helpers::formatRemoveDecimal($prod_list['m_actual_qty']) : '-'?> </td>
              <td colspan="3" class="midtext txt_center f_bold b_4 <?=$m_balance?>"><?=($prod_list['m_balance'] != '-') ? Helpers::formatRemoveDecimal($prod_list['m_balance']) : '-'?></td>

            </tr>
          <? }
          ?>
          </tr>
          </tbody>
        </table>
      </div>
      <? if (Yii::$app->params['deviation'] > 0) { ?>
        <!--        KamoliddinAka Rustamov talabi bilan hide qilingan-->
        <div class="pull-right" style="display: none"><span class="text-red">*</span>
          <i class="text-muted"><?=Yii::t('app', 'Deviation(±10%)')?></i></div>
      <? } ?>
    </div>
  <? } else { ?>
    <div class='col-xs-12 alert alert-danger alert-dismissible text-center'>
      <h1><?=Yii::t('app', 'Data not found')?></h1>
    </div>
  <? } ?>
</div>

<?
$this->registerCssFile("@themes/css/jquery-confirm.min.css", ['depends' => [AdminLteAsset::className()]]);
$this->registerJsFile("@themes/js/jquery-confirm.min.js", ['depends' => [JqueryAsset::className()]]);
?>

<?
$script1 = <<< JS
$(document).ready(function() {
    $('#fix_table').tableFixer({'left' : 2});
    changeHeight();
    $(window).resize(function(){
        changeHeight();
    });
    function changeHeight(){
    window_h = $(window).height();
    table_h = window_h - 270;
    // console.log(window_h+"-"+table_h);
    $('#div_fix_table').height(table_h+'px');
    }

    $('#download-xls').on('click', function (e) {
        html_xls_export('fix_table', '$downloadFileName');
    });
    
    if($partCount == true) {
    	$('#b1_m').html($b1_m)
      $('#b1_0').html($b1_0)
      $('#b1_p').html($b1_p)
      $('#b2_m').html($b2_m)
      $('#b2_0').html($b2_0)
      $('#b2_p').html($b2_p)
      $('#b3_m').html($b3_m)
      $('#b3_0').html($b3_0)
      $('#b3_p').html($b3_p)
      $('#b4_m').html($b4_m)
      $('#b4_0').html($b4_0)
      $('#b4_p').html($b4_p)
    }

	$('.not_modal').click(function(){
      $.dialog({
        title: false,
        cancelButton: true,
        confirmButton: false,
        backgroundDismiss: true,
        closeIcon: false,
        columnClass: 'txt_center col-xs-12 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4',
        content: $(this).find('.comment_txt').text(),
      });
	});
});
JS;
$this->registerJs($script1);
?>

