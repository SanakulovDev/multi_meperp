<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use app\models\Part;
use app\models\ProductSpecification;

/**
 * This is the model class for table "pechat_product".
 *
 * @property int $id
 * @property int|null $part_id
 * @property string|null $number_lot
 * @property string|null $date
 * @property int|null $weight_netto
 * @property int|null $weight_brutto
 * create date 11-05-2023
 * Sanakulov Anvar
 * #sanakulov_dev
 */
class Dashboard extends \yii\db\ActiveRecord
{
    
    
    public static function shablon($title, $models)
    {
        $data = '<div class="col-md-12">';
        $data .= '<h4 class="text-uppercase font-weight-bold" style="font-weight: bold;">'.$title.'</h4>';
        $data .= '<table class="table">';
        $data .= '<thead class="">';
        $data .= '<tr class="text-center">';
        $data .= '<th class="text-center">№</th>';
        $data .= '<th class="text-center">Наименование</th>';
        $data .= '<th class="text-center">№ Продукта</th>';
        $data .= '<th class="text-center">Кол-во</th>';
        $data .= '<th class="text-center">Ед. изм</th>';
        $data .= '<th class="text-center">Примечание</th>';
        $data .= '</tr>';
        $data .= '</thead>';
        $data .= '<tbody>';
        $i = 1;
        foreach($models as $model){
            $data .= '<tr>';
            $data .= '<td class="text-center" style="width: 100px;">'.$i.'</td>';
            $data .= '<td class="text-center" style="width: 500px;">'.$model['part_name'].'</td>';
            $data .= '<td class="text-right" style="width: 200px;">'.$model['part_no'].'</td>';
            $data .= '<td class="text-right" style="width: 200px;">'.sprintf("%0.2f", $model['quantity']).'</td>';
            $data .= '<td class="text-center" style="width: 100px;">кг</td>';
            $receiver = '';
            if(isset($model['receiver'])){
                $receiver = $model['receiver'];
            }
            $data .= '<td class="text-center">'.$receiver.'</td>';
            $data .= '</tr>';
            $i++;
        }
        $data .= '</tbody>';
        $data .= '</table>';
        $data .= '</div>';
        return $data;
    }
    public static function fakt($date=null)
    {
        $date = self::runDate();
        $query = " SELECT part.id as part_id, part.part_name as part_name, part.part_no part_no, sum(po.quantity) as quantity, part.part_color as part_color from production_order po 
            LEFT JOIN part ON part.id = po.part_id where 
            DATE(FROM_UNIXTIME(po.created_at))='".$date."' 
            group by po.part_id;
        ";
        $result = Yii::$app->db->createCommand($query)->queryAll();
        return $result;
    }

    // ttn - отгружено
    public static function ttn($date = null)
    {
        $date = self::runDate();
        $query = " SELECT fgd.part_name as part_name, fgd.part_no as part_no, sum(fgd.qty) as quantity, c.name as receiver from fg_invoice_detail fgd 
            LEFT JOIN fg_invoice  ON fgd.fg_invoice_id = fg_invoice.id 
            INNER JOIN customer c ON c.id = fg_invoice.customer_id
            where 
            DATE(FROM_UNIXTIME(fgd.created_at))='".$date."' 
            group by fgd.part_name, fgd.part_no, c.id;
        ";
        $result = Yii::$app->db->createCommand($query)->queryAll(); 
        return $result;
    }
    // prixod - приход
    public static function prixod($date = null)
    {
        $date = self::runDate();
        $query = " SELECT part.part_name as part_name, part.part_no as part_no, sum(qty) as quantity, sp.name as receiver from document_detail dd 
            LEFT JOIN document d ON d.id = dd.document_id 
            LEFT JOIN part ON part.id = dd.part_id 
            INNER JOIN supplier sp ON sp.id = d.supplier_id 
            where 
            DATE(FROM_UNIXTIME(d.created_at))='".$date."' 
            and d.to_warehouse_id = 1 and d.status = 1
            and sp.name IS NOT NULL
            group by dd.part_id, sp.id
        
        ";
        $result = Yii::$app->db->createCommand($query)->queryAll(); 
        return $result;
    }


    // norma rasxoda
    public static function normaRasxoda($part_id, $date = null, $quantity)
    {
        $date = self::runDate();
        $productSpecification = ProductSpecification::find()->where(['part_id' => $part_id])->andWhere(['status'=>1])->one();
        $qisqartma = 1;
        if($productSpecification){
            $qisqartma  = $quantity / $productSpecification->amount;

        }
        $query = " SELECT part.part_name, part.part_no, part.part_color as part_color,  usage_qty * '".$qisqartma."'  as quantity FROM product_specification_item psi 
            LEFT JOIN part ON part.id = psi.part_id 
            where 
            psi.product_specification_id = '".$productSpecification->id."'

        ";
        
        $result = Yii::$app->db->createCommand($query)->queryAll(); 
        return $result;
    }

    // production plan
    public static function productionPlan($date = null)
    {
        $date = self::runDate();
        $query = " SELECT part.part_name as part_name, part.part_color as part_color, production_plan.part_id as part_id, sum(target_qty) as quantity from production_plan
            LEFT JOIN part ON part.id = production_plan.part_id 
            where 
            production_date='".$date."' and status = 1
            group by production_plan.part_id
        ";
        $result = Yii::$app->db->createCommand($query)->queryAll(); 
        return $result;
    }

    public static function runDate()
    {
        // $date = date('H:i');
        // return '2022-10-02';
        // return '2022-08-03';

        // return '2021-05-29';
        // return '2023-05-15';
        if($date < '08:00'){
            return date('Y-m-d', strtotime('-2 day'));
        }
        return date('Y-m-d', strtotime('-1 day'));
    }

    // from dashboard analiz 

    public static function todayProductionPlanPartList($date=null)
    {
        if(empty($date)){
            $date = date('Y-m-d');
        }
        $query = "SELECT part_id, line, shift from production_plan where production_date='".$date."' and line is not null group by part_id, line, shift";
        $response = Yii::$app->db->createCommand($query)->queryAll();
        return $response;
        
    }
    public static function todayProductionPlan($part_id, $line, $shift, $date)
    {
        $query = "SELECT sum(target_qty) as qty from production_plan where part_id='".$part_id."' and line='".$line."' and shift='".$shift."' and production_date='".$date."'";
        $response = Yii::$app->db->createCommand($query)->queryOne();
        return $response['qty']?:0;
    }
    public static function todayProductionFakt($part_id, $line, $shift, $date)
    {
        $query = "SELECT sum(quantity) as qty from production_order where part_id='".$part_id."' and line='".$line."' and DATE(FROM_UNIXTIME(created_at))='".$date."'";
        $response = Yii::$app->db->createCommand($query)->queryOne();
        return $response['qty']?:0;
    }
    public static function todayProductionByData()
    {
        $date = date('Y-m-d', time());
        $partList = self::todayProductionPlanPartList($date);
        $nowTime = date('d.m.Y H:i:s', time()).' AM';
        $data['nowTime'] = $nowTime;
        $data['data'] = [];
        foreach($partList as $part){
            $data['data'][] = [
                'part_id'       => $part['part_id'],
                'part_name'     => substr(Part::getPartName($part['part_id']), 0, 45),
                'line'          => $part['line'].'-'.Yii::t('app', 'Line'),
                'shift'         => $part['shift'].'-'.Yii::t('app', 'Shift'),
                'plan'          => self::todayProductionPlan($part['part_id'], $part['line'], $part['shift'], $date)*1,
                'fakt'          => self::todayProductionFakt($part['part_id'], $part['line'], $part['shift'], $date)*1,
                'balance'       => self::todayProductionPlan($part['part_id'], $part['line'], $part['shift'], $date) - self::todayProductionFakt($part['part_id'], $part['line'], $part['shift'], $date),
            ];
        }
        return $data;
    }
    public static function todayProductionByHtml()
    {
        $data = self::todayProductionByData();
        $html = '';
        foreach($data['data'] as $model){
            $html .= '<div class="item-row">';
            $html .= '<div class="row " style="margin: 50px 0 25px 0;">';
            $html .= '<div class="col-md-3 text-left">';
            $html .= '<span class="color-primary">'.$model['part_name'].'</span>';
            $html .= '</div>';
            $html .= '<div class="col-md-6 ">';
            $html .= '<div class="row">';
            $html .= '<div class="col-md-6 text-right">';
            $html .= '<span class="color-primary">'.$model['line'].'</span>';
            $html .= '</div>';
            $html .= '<div class="col-md-6 text-right">';
            $html .= '<span class="color-primary">'.$model['shift'].'</span>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '<div class="row ">';
            $html .= '<div class="col-md-3 item-border-right item-quantity">';
            //span header title
            $html .= '<span class="color-primary item-quantity-title">'.Yii::t('app', 'Plan').'</span>';
            $html .= '<span class="color-success">'.divideString($model['plan'], 3).'</span>';
            $html .= '</div>';
            $html .= '<div class="col-md-3 item-border-right item-quantity">';
            $html .= '<span class="color-primary item-quantity-title">'.Yii::t('app', 'Fakt').'</span>';
            $html .= '<span class="color-success">'.divideString($model['fakt'], 3).'</span>';
            $html .= '</div>';
            $html .= '<div class="col-md-3 item-quantity">';
            $html .= '<span class="color-primary item-quantity-title">'.Yii::t('app', 'Balance').'</span>';
            $html .= '<span class="color-danger">'.divideString($model['balance'], 3).'</span>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }
        return $html;
    }

    
}
