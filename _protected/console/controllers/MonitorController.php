<?php

namespace app\console\controllers;

use app\components\Helpers;
use app\models\PartProductionMonitor;
use app\models\ProductionMonitor;
use Yii;
use yii\console\Controller;

class MonitorController extends Controller
{

  public function actionIndex()
  {

    // Joriy shift ma'lumotlarni olish
    $shiftData = Helpers::getShift();
    $shift = $shiftData['shift'];
    $date = $shiftData['productionDate'];

    // Production plan tabledan joriy shift ga tegishli ma'lumotlarni olish
    $query = "select * from production_plan where  target_qty <> 0 and production_date = :date and shift = :shift order by warehouse_id, part_id";
    $plan = Yii::$app->db->createCommand($query, [':date' => $date,':shift' => $shift])->queryAll();
  
    // Monitor tablelardan joriy shift ga tegishli ma'lumotlarni olish
    $query = "
      select * from 
        production_monitor pm, part_production_monitor ppm 
      where
        pm.id = ppm.production_monitor_id and
        pm.production_date = :date and
        pm.shift = :shift
      order by 
        pm.warehouse_id , ppm.part_id 
    ";
    $monitor = Yii::$app->db->createCommand($query, [':date' => $date,':shift' => $shift])->queryAll();
    
    $transaction = Yii::$app->db->beginTransaction();
    $error = false;
    $i = 0;
    foreach ($plan as $rplan) {

      $isExists = false;
      foreach ($monitor as $rmon) {
        if( $rplan['warehouse_id'] == $rmon['warehouse_id'] and $rplan['part_id'] == $rmon['part_id'] ){
          $isExists = true;
          break;
        }
      }
      
      if(!$isExists){

        // Monitor table larga ma'lumot yozish
        $resultParent = ProductionMonitor::write($rplan['warehouse_id'], $date, $shift);
        if($resultParent['status'] == 0){
          $error = true;
          break;
        }

        $resultChild = PartProductionMonitor::setProduced($resultParent['data']->id, $rplan['part_id'],0);
         if(!$resultChild){
          $error = true;
          break;
        }

      }

    }

    if(!$error){
      $transaction->commit();
      echo "Process successfully finished\n";
    } else {
      echo "Process failed. Something went wrong\n";
      $transaction->rollBack();  
    }  
    
  }

}
