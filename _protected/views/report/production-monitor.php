<?php
use app\assets\AdminLteAsset;
use app\components\Helpers;
use app\models\Warehouse;
use kartik\datetime\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JqueryAsset;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/** @var $fromDate */
/** @var $toDate */
/** @var $needWarehouseId */
$this->title = Yii::t('app', 'Production results');
$this->params['breadcrumbs'][] = $this->title;
$dataCount = count($data);
?>
<div class="row">
  <?php $form = ActiveForm::begin(['action' => ['production-monitor'], 'method' => 'get',]); ?>
  <div class="col-md-2 col-lg-2">
    <?php echo DateTimePicker::widget(
      [
        'name' => 'from_date',
        'value' => $fromDate,
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
          'placeholder' => Yii::t('app', 'From'),
          'class' => ' form-control'
        ]
      ]
    ); ?>
  </div>
  <div class="col-md-2 col-lg-2">
    <?php echo DateTimePicker::widget(
      [
        'name' => 'to_date',
        'value' => $toDate,
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
          'placeholder' => Yii::t('app', 'To'),
          'class' => ' form-control'
        ]
      ]
    ); ?>
  </div>
  <div class="col-md-2 col-lg-2">
    <?php
      $warehouses = Warehouse::find()
                             ->select(['id', 'name'])
                             ->where(['status' => Warehouse::STATUS_ACTIVE, 'warehouse_type' => Warehouse::TYPE_SHOP]);
      $warehouses = $warehouses->all();
      $warehouses_list = ArrayHelper::map($warehouses, 'id', 'name');
      $warehouses_list = [0 => Yii::t('app', 'Warehouse')] + $warehouses_list;
      echo Html::dropDownList('from_warehouse_id', $needWarehouseId, $warehouses_list, ['class' => 'form-control select2']);
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
  <?php
  if ($dataCount == true) { ?>
    <div class="col-md-12 col-lg-12">
      <div id="div_fix_table" class="col-md-12  col-lg-12">
        <table id="fix_table" class="table table-striped table-bordered table-condensed table-sm-padding_2_0">
          <thead>
          <tr>
            <th class="txt_center"><?=Yii::t('app', 'Line')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Part No')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Part name')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Production date')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Shift')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Production time (min)')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Planned line stop (min)')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Not planned line stop (min)')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Production part qty')?></th>
            <th class="txt_center"><?=Yii::t('app', 'OK')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Repair')?></th>
            <th class="txt_center"><?=Yii::t('app', 'Defect')?></th>
          </tr>
          </thead>
          <tbody>
          <?php
            foreach ($data as $item) {
              $ok = $item['produced_qty'] ? ($item['produced_qty'] - $item['repaired_qty'] - $item['broken_qty']) : 0;
          ?>
            <tr>
              <td class="txt_center"><?=$item['warehouse_name']?></td>
              <td class="txt_center"><?=$item['part_no'].' '.$item['part_color']?></td>
              <td class="txt_center"><?=$item['part_name']?></td>
              <td class="txt_center"><?=$item['production_date']?></td>
              <td class="txt_center"><?=$item['shift']?></td>
              <td class="txt_center"><?=$item['actual_production_time']?></td>
              <td class="txt_center"><?=$item['planned']?></td>
              <td class="txt_center"><?=$item['not_planned']?></td>
              <td class="txt_center"><?=Helpers::formatRemoveDecimal($item['produced_qty'])?></td>
              <td class="txt_center"><?=Helpers::formatRemoveDecimal($ok)?></td>
              <td class="txt_center"><?=$item['repaired_qty']?></td>
              <td class="txt_center"><?=$item['broken_qty']?></td>
            </tr>
          <?php } ?>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
  <?php } else { ?>
    <div class='col-xs-12 alert alert-danger alert-dismissible text-center'>
      <h1><?=Yii::t('app', 'Data not found')?></h1>
    </div>
  <?php } ?>
</div>

<?php
$this->registerCssFile("@themes/css/jquery-confirm.min.css", ['depends' => [AdminLteAsset::className()]]);
$this->registerJsFile("@themes/js/jquery-confirm.min.js", ['depends' => [JqueryAsset::className()]]);
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
    $('#div_fix_table').height(table_h+'px');
    }

    $('#download-xls').on('click', function (e) {
        html_xls_export('fix_table', '$downloadFileName');
    });
	
});
JS;
$this->registerJs($script1);
?>

