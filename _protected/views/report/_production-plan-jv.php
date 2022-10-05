<?php
use app\models\Part;
use app\models\ProductModel;
use app\models\Warehouse;
use kartik\datetime\DateTimePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DeliveryPlanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $need_month app\controllers\DeliveryPlanController */
/** @var TYPE_NAME $DB_part_list */
/** @var TYPE_NAME $part_id */
/** @var TYPE_NAME $warehouse_id */
/** @var $table_array app\controllers\ReportController*/
/** @var TYPE_NAME $downloadFileName */
$this->title = Yii::t('app', 'Production plan');
$this->params['breadcrumbs'][] = $this->title;
$month_end = date("Y-m-t", strtotime($need_month));
$month_end_date = date('t', strtotime($month_end));
?>
<div class="row">
  <div style="clear: both;"></div>
  <div class="col-xs-6 col-sm-2">
    <?=DateTimePicker::widget(
      [
        'name' => 'need_month',
        'value' => $need_month,
        'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
        'layout' => '{picker}{input}{remove}',
        'removeButton' => ['position' => 'append'],
        'language' => Yii::$app->language,
        'pluginOptions' => [
          'autoclose' => true,
          'format' => 'yyyy-mm',
          'startView' => 'year',
          'minView' => 'year',
          'maxView' => 'year',
        ],
        'options' => [
          'autocomplete' => 'off',
          'placeholder' => Yii::t('app', 'need_month'),
          'class' => ' form-control'
        ]
      ]
    );?>
  </div>
  <div class="col-xs-6 col-sm-2">
    <?
    $model->warehouse_id = $warehouse_id;
    $warehouses = Warehouse::find()
                           ->select(['warehouse.id as id', 'warehouse.name as name'])
                           ->leftJoin('user_warehouse', 'warehouse.id = user_warehouse.warehouse_id')
                           ->where(['user_warehouse.user_id' => Yii::$app->user->id])
                           ->all();
    $warehouses_list = ArrayHelper::map($warehouses, 'id', 'name');
    $warehouses_list = [0 => Yii::t('app', 'Line')] + $warehouses_list;
    $params = ['class' => 'form-control', 'id' => 'warehouse_id'];
    echo Select2::widget([
      'theme' => Select2::THEME_DEFAULT,
      'model' => $model,
      'value' => $warehouse_id,
      'attribute' => 'warehouse_id',
      'data' => $warehouses_list,
      'options' => [$params],
    ]);
    ?>
  </div>
  <div class="col-xs-6 col-sm-2">
    <?
    $model->part_id = $part_id;
    $parts_list = ArrayHelper::map(Part::find()->all(), 'id', 'part_no');
    $parts_list = [0 => Yii::t('app', 'Part')] + $parts_list;
    $params = ['class' => 'form-control', 'placeholder' => Yii::t('app', 'Part')];
    echo Select2::widget([
      'theme' => Select2::THEME_DEFAULT,
      'model' => $model,
      'value' => $part_id,
      'attribute' => 'part_id',
      'data' => $parts_list,
      'options' => [$params],
    ]);
    ?>
  </div>
<!--  <div class="col-xs-6 col-sm-2">-->
<!--    --><?//
//    $sel_product_model = isset($_POST['product_model']) ? $_POST['product_model'] : null;
//    $product_model = ArrayHelper::map(ProductModel::find()->all(), 'id', 'modelname');
//    $product_model = [0 => Yii::t('app', 'Model')] + $product_model;
//    $params = ['class' => 'form-control', 'placeholder' => Yii::t('app', 'Model')];
//    echo Select2::widget([
//      'theme' => Select2::THEME_DEFAULT,
//      'model' => $model,
//      'value' => $sel_product_model,
//      'name' => 'product_model',
//      'data' => $product_model,
//      'options' => [$params],
//    ]);
//    ?>
<!--  </div>-->
  <div class="col-xs-8 col-sm-2">
    <?=Html::submitButton(Yii::t('app', 'btn-show'), ['class' => 'btn btn-primary'])?>
  </div>

  <div class="col-xs-4 col-sm-2">
    <div class="form-group pull-right">
      <?=Html::button(Yii::t('app', 'btn-download-delivery-plan'), ['class' => 'btn btn-info', 'id' => 'download_xls_jv'])?>
    </div>
  </div>

</div>
<br>
<div class="row">
  <div class="div_fix_table col-md-12  col-lg-12">
    <table id="fix_table_jv" class="table table-striped table-bordered table-condensed table-sm-padding_2_0">
      <thead>
      <tr>
        <th rowspan="2"><?=Yii::t('app', 'Line')?></th>
        <th rowspan="2"><?=Yii::t('app', 'Product no')?></th>
<!--        <th rowspan="2">--><?//=Yii::t('app', 'Model')?><!--</th>-->
        <? for ($i = 1; $i <= $month_end_date; $i++) { ?>
          <th colspan="2" style="text-align:center"> <?=sprintf("%02d", $i)?> </th>
        <? } ?>
        <th rowspan="2"><?=Yii::t('app', 'Total')?></th>
      </tr>
      <tr>
        <? for ($i = 1; $i <= $month_end_date; $i++) { ?>
          <th style="text-align:center">1</th>
          <th style="text-align:center">2</th>
        <? } ?>
      </tr>
      </thead>
      <tbody>
      <?foreach ($table_array as $prod_list) {?>
        <tr>
          <td class="midtext text-center td-nowrap" style="font-size:70%;"><?=$prod_list['wh_nm']?></td>
          <td class="midtext text-center"><?=$prod_list['part_no']?></td>
<!--          <td class="midtext text-center td-nowrap" style="font-size:70%;">--><?//=$prod_list['product_model']?><!--</td>-->
          <?
          $amt = 0;
          for ($i = 0; $i < $month_end_date; $i++) {
            $prod_sana = date('Y-m-d', strtotime($need_month.'-01 +'.$i.' day'));
            ?>
            <td class="midtext text-center">
              <?
              if (isset($prod_list[$prod_sana])) {
                if (isset($prod_list[$prod_sana][0])) {
                  echo $a = ($prod_list[$prod_sana][0]) ? : 0;
                } else {
                  echo $a = 0;
                }
              } else {
                echo $a = 0;
              }
              $amt += $a;
              ?>
            </td>
            <td class="midtext text-center">
              <?
              if (isset($prod_list[$prod_sana])) {
                echo $a = ($prod_list[$prod_sana][1] ?? null) ? : 0;
              } else {
                echo $a = 0;
              }
              $amt += $a;
              ?>
            </td>
          <? } ?>
          <td class="midtext text-right text-bold">
            <? echo $amt; ?>
          </td>
        </tr>
      <? } ?>
      </tbody>
    </table>
  </div>
</div>
