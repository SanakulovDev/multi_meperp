<?php
namespace app\controllers;

use Yii;

class SettingController extends AppController {

  public function actionIndex() {
    if(Yii::$app->request->post()) {
      $shift_hour = (isset($_POST['shift_hour']) && strlen(trim($_POST['shift_hour'])) > 0) ? $_POST['shift_hour'] : 11;
      $shift1_begin = (isset($_POST['shift1_begin']) && strlen(trim($_POST['shift1_begin'])) > 0) ? $_POST['shift1_begin'] : '08:00';
      $shift2_begin = (isset($_POST['shift2_begin']) && strlen(trim($_POST['shift2_begin'])) > 0) ? $_POST['shift2_begin'] : '20:00';
      $shift1_end = date('H:s', strtotime(date('Y-m-d', time()).' '.$shift2_begin) - 1);
      $shift2_end = date('H:s', strtotime(date('Y-m-d', time()).' '.$shift1_begin) - 1);

      $eolTab = PHP_EOL."\t";
      $setting_file = Yii::$app->basePath."/config/params.php";
      $setting = "<?php".PHP_EOL."return [";
      $setting .= $eolTab."'access-token' => '5cPTsGiB0VXIsNLpxTqc',";
      $setting .= $eolTab."'shift_hour' => ".$shift_hour.",";
      $setting .= $eolTab."'shifts' => [";
      $setting .= $eolTab."\t"."1 => ['".$shift1_begin."', '".$shift1_end."', ''],";
      $setting .= $eolTab."\t"."2 => [['".$shift2_begin."', '23:59', ''], ['00:00', '".$shift2_end."', ' - 1 days']]";
      $setting .= $eolTab."],";
      $setting .= $eolTab.((isset($_POST['comp_name']) && strlen($_POST['comp_name']) > 0) ? "'comp_name'=>'".$_POST['comp_name']."'," : "'comp_name'=>'',");
      $setting .= $eolTab.((isset($_POST['comp_short_name']) && strlen($_POST['comp_short_name']) > 0) ? "'comp_short_name'=>'".$_POST['comp_short_name']."'," : "'comp_short_name'=>'',");
      $setting .= $eolTab.((isset($_POST['account_suffix']) && strlen($_POST['account_suffix']) > 0) ? "'account_suffix'=>'".$_POST['account_suffix']."'," : "'account_suffix'=>'',");
      $setting .= $eolTab.((isset($_POST['comp_duns']) && strlen($_POST['comp_duns']) > 0) ? "'comp_duns'=>'".$_POST['comp_duns']."'," : "'comp_duns'=>'',");
      $setting .= $eolTab.((isset($_POST['adminEmail']) && strlen($_POST['adminEmail']) > 0) ? "'adminEmail'=>'".$_POST['adminEmail']."'," : "'adminEmail'=>'',");
      $setting .= $eolTab.((isset($_POST['supportEmail']) && strlen($_POST['supportEmail']) > 0) ? "'supportEmail'=>'".$_POST['supportEmail']."'," : "'supportEmail'=>'',");
      $setting .= $eolTab.((isset($_POST['showKdCode']) && strlen($_POST['showKdCode']) > 0) ? "'showKdCode'=>true," : "'showKdCode'=>false,");
      $setting .= $eolTab.((isset($_POST['ga2shop']) && strlen($_POST['ga2shop']) > 0) ? "'ga2shop'=>".$_POST['ga2shop']."," : "'ga2shop'=>0,");
      $setting .= $eolTab.((isset($_POST['vat']) && strlen($_POST['vat']) > 0) ? "'vat'=>".$_POST['vat']."," : "'vat'=>0,");
      $setting .= $eolTab.((isset($_POST['excise']) && strlen($_POST['excise']) > 0) ? "'excise'=>".$_POST['excise']."," : "'excise'=>0,");
      $setting .= $eolTab.((isset($_POST['deviation']) && strlen($_POST['deviation']) > 0) ? "'deviation'=>".$_POST['deviation']."," : "'deviation'=>0,");
      $setting .= $eolTab.((isset($_POST['plan_freeze_time']) && strlen($_POST['plan_freeze_time']) > 0) ? "'plan_freeze_time'=>".$_POST['plan_freeze_time']."," : "'plan_freeze_time'=>1,");
      $setting .= $eolTab.((isset($_POST['fact_freeze_time']) && strlen($_POST['fact_freeze_time']) > 0) ? "'fact_freeze_time'=>".$_POST['fact_freeze_time']."," : "'fact_freeze_time'=>10,");
      $setting .= $eolTab.((isset($_POST['uzAutoMotorsId']) && strlen($_POST['uzAutoMotorsId']) > 0) ? "'uzAutoMotorsId'=>".$_POST['uzAutoMotorsId']."," : "'uzAutoMotorsId'=>0,");
      $setting .= $eolTab.((isset($_POST['user_device_type']) && strlen($_POST['user_device_type']) > 0) ? "'user_device_type'=>'".$_POST['user_device_type']."'," : "'user_device_type'=>'',");
      $setting .= $eolTab.((isset($_POST['logxWhId']) && strlen($_POST['logxWhId']) > 0) ? "'logxWhId'=>".$_POST['logxWhId']."," : "'logxWhId'=>0,");
      $setting .= $eolTab.((isset($_POST['kdWhId']) && strlen($_POST['kdWhId']) > 0) ? "'kdWhId'=>".$_POST['kdWhId']."," : "'kdWhId'=>0,");
      $setting .= $eolTab.((isset($_POST['deliveryWhId']) && strlen($_POST['deliveryWhId']) > 0) ? "'deliveryWhId'=>".$_POST['deliveryWhId']."," : "'deliveryWhId'=>0,");
      $setting .= $eolTab.((isset($_POST['inTransitWhId']) && strlen($_POST['inTransitWhId']) > 0) ? "'inTransitWhId'=>".$_POST['inTransitWhId']."," : "'inTransitWhId'=>0,");
      $setting .= $eolTab.((isset($_POST['outsoursingWhId']) && strlen($_POST['outsoursingWhId']) > 0) ? "'outsoursingWhId'=>".$_POST['outsoursingWhId']."," : "'outsoursingWhId'=>0,");
      $setting .= $eolTab.((isset($_POST['adjustmentWhId']) && strlen($_POST['adjustmentWhId']) > 0) ? "'adjustmentWhId'=>".$_POST['adjustmentWhId']."," : "'adjustmentWhId'=>0,");
      $setting .= $eolTab.((isset($_POST['fg_wh_ids']) && count($_POST['fg_wh_ids']) > 0) ? "'fg_wh_ids'=>[".implode(",", $_POST['fg_wh_ids'])."]," : "'fg_wh_ids'=>[0],");
      $setting .= $eolTab.((isset($_POST['damage_wh_ids']) && count($_POST['damage_wh_ids']) > 0) ? "'damage_wh_ids'=>[".implode(",", $_POST['damage_wh_ids'])."]," : "'damage_wh_ids'=>[0],");
      $setting .= $eolTab.((isset($_POST['import_contract_source_ids']) && count($_POST['import_contract_source_ids']) > 0) ? "'import_contract_source_ids'=>[".implode(",", $_POST['import_contract_source_ids'])."]," : "'import_contract_source_ids'=>[0],");
      $setting .= $eolTab.((isset($_POST['local_contract_source_ids']) && count($_POST['local_contract_source_ids']) > 0) ? "'local_contract_source_ids'=>[".implode(",", $_POST['local_contract_source_ids'])."]," : "'local_contract_source_ids'=>[0],");
      $setting .= $eolTab.((isset($_POST['cutted_coil_part_type_id']) && strlen($_POST['cutted_coil_part_type_id']) > 0) ? "'cutted_coil_part_type_id'=>".$_POST['cutted_coil_part_type_id']."," : "'cutted_coil_part_type_id'=>0,");
      $setting .= $eolTab.((isset($_POST['consignment_contract_source_ids']) && count($_POST['consignment_contract_source_ids']) > 0) ? "'consignment_contract_source_ids'=>[".implode(",", $_POST['consignment_contract_source_ids'])."]," : "'consignment_contract_source_ids'=>[0],");
      $setting .= $eolTab.((isset($_POST['semi_contract_source_ids']) && count($_POST['semi_contract_source_ids']) > 0) ? "'semi_contract_source_ids'=>[".implode(",", $_POST['semi_contract_source_ids'])."]," : "'semi_contract_source_ids'=>[0],");
      $setting .= $eolTab.((isset($_POST['semi_production_line_ids']) && count($_POST['semi_production_line_ids']) > 0) ? "'semi_production_line_ids'=>[".implode(",", $_POST['semi_production_line_ids'])."]," : "'semi_production_line_ids'=>[0],");
      $setting .= $eolTab.((isset($_POST['less_dates_count']) && strlen($_POST['less_dates_count']) > 0) ? "'less_dates_count'=>".$_POST['less_dates_count']."," : "'less_dates_count'=>0,");
      $setting .= $eolTab.((isset($_POST['greater_dates_count']) && strlen($_POST['greater_dates_count']) > 0) ? "'greater_dates_count'=>".$_POST['greater_dates_count']."," : "'greater_dates_count'=>0,");
      $setting .= $eolTab.((isset($_POST['shipment_dates_count']) && strlen($_POST['shipment_dates_count']) > 0) ? "'shipment_dates_count'=>".$_POST['shipment_dates_count']."," : "'shipment_dates_count'=>0,");
      $setting .= PHP_EOL."];";
      file_put_contents($setting_file, $setting, LOCK_EX);
      Yii::$app->session->setFlash('success', Yii::t('app', 'Successfully'));
    }
    $all_params = require(Yii::$app->request->baseUrl.'_protected/config/params.php');
    $all_params['comp_name'] = isset($all_params['comp_name']) ? $all_params['comp_name'] : 'XXX';
    $all_params['comp_short_name'] = isset($all_params['comp_short_name']) ? $all_params['comp_short_name'] : 'XXX';
    $all_params['account_suffix'] = isset($all_params['account_suffix']) ? $all_params['account_suffix'] : 'XXX';
    $all_params['comp_duns'] = isset($all_params['comp_duns']) ? $all_params['comp_duns'] : 'XXX';
    $all_params['adminEmail'] = isset($all_params['adminEmail']) ? $all_params['adminEmail'] : 'example@comp.uz';
    $all_params['supportEmail'] = isset($all_params['supportEmail']) ? $all_params['supportEmail'] : 'example@comp.uz';
    $all_params['shift_hour'] = isset($all_params['shift_hour']) ? $all_params['shift_hour'] : '11';
    $all_params['shift1_begin'] = isset($all_params['shifts'][1][0]) ? $all_params['shifts'][1][0] : '08:00';
    $all_params['shift2_begin'] = isset($all_params['shifts'][2][0][0]) ? $all_params['shifts'][2][0][0] : '20:00';
    $all_params['showKdCode'] = isset($all_params['showKdCode']) ? $all_params['showKdCode'] : 0;
    $all_params['ga2shop'] = isset($all_params['ga2shop']) ? $all_params['ga2shop'] : 0;
    $all_params['vat'] = isset($all_params['vat']) ? $all_params['vat'] : 0;
    $all_params['excise'] = isset($all_params['excise']) ? $all_params['excise'] : 0;
    $all_params['plan_freeze_time'] = isset($all_params['plan_freeze_time']) ? $all_params['plan_freeze_time'] : 1;
    $all_params['fact_freeze_time'] = isset($all_params['fact_freeze_time']) ? $all_params['fact_freeze_time'] : 10;
    $all_params['deviation'] = isset($all_params['deviation']) ? $all_params['deviation'] : 0;
    $all_params['uzAutoMotorsId'] = isset($all_params['uzAutoMotorsId']) ? $all_params['uzAutoMotorsId'] : 0;
    $all_params['user_device_type'] = isset($all_params['user_device_type']) ? $all_params['user_device_type'] : 0;
    $all_params['logxWhId'] = isset($all_params['logxWhId']) ? $all_params['logxWhId'] : 0;
    $all_params['kdWhId'] = isset($all_params['kdWhId']) ? $all_params['kdWhId'] : 0;
    $all_params['deliveryWhId'] = isset($all_params['deliveryWhId']) ? $all_params['deliveryWhId'] : 0;
    $all_params['inTransitWhId'] = isset($all_params['inTransitWhId']) ? $all_params['inTransitWhId'] : 0;
    $all_params['outsoursingWhId'] = isset($all_params['outsoursingWhId']) ? $all_params['outsoursingWhId'] : 0;
    $all_params['adjustmentWhId'] = isset($all_params['adjustmentWhId']) ? $all_params['adjustmentWhId'] : 0;
    $all_params['fg_wh_ids'] = isset($all_params['fg_wh_ids']) ? $all_params['fg_wh_ids'] : 0;
    $all_params['damage_wh_ids'] = isset($all_params['damage_wh_ids']) ? $all_params['damage_wh_ids'] : [0];
    $all_params['import_contract_source_ids'] = isset($all_params['import_contract_source_ids']) ? $all_params['import_contract_source_ids'] : [0];
    $all_params['local_contract_source_ids'] = isset($all_params['local_contract_source_ids']) ? $all_params['local_contract_source_ids'] : [0];
    $all_params['cutted_coil_part_type_id'] = isset($all_params['cutted_coil_part_type_id']) ? $all_params['cutted_coil_part_type_id'] : [0];
    $all_params['consignment_contract_source_ids'] = isset($all_params['consignment_contract_source_ids']) ? $all_params['consignment_contract_source_ids'] : [0];
    $all_params['semi_contract_source_ids'] = isset($all_params['semi_contract_source_ids']) ? $all_params['semi_contract_source_ids'] : [0];
    $all_params['semi_production_line_ids'] = isset($all_params['semi_production_line_ids']) ? $all_params['semi_production_line_ids'] : [0];
    $all_params['less_dates_count'] = isset($all_params['less_dates_count']) ? $all_params['less_dates_count'] : 0;
    $all_params['greater_dates_count'] = isset($all_params['greater_dates_count']) ? $all_params['greater_dates_count'] : 0;
    $all_params['shipment_dates_count'] = isset($all_params['shipment_dates_count']) ? $all_params['shipment_dates_count'] : 0;
    return $this->render('index', ['all_params' => $all_params]);
  }

}


