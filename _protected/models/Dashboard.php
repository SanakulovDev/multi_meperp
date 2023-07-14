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
    
    
    public static function shablon($title, $models, $class=null)
    {
        $data = '<div class="col-md-12">';
        $data .= '<h4 class="text-uppercase font-weight-bold" style="font-weight: bold;">'.$title.'</h4>';
        $data .= '<table class="table " id="table-'.$class.'">';
        $data .= '<thead class="">';
        $data .= '<tr class="text-center">';
        $data .= '<th class="text-center">№</th>';
        $data .= '<th class="text-center">Наименование ('.$title.')</th>';
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
        if(empty($date)){
            $date = self::runDate();
        }
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
        if(empty($date)){
            $date = self::runDate();
        }
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
        if(empty($date)){
            $date = self::runDate();
        }
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
        if(empty($date)){
            $date = self::runDate();
        }
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

    public static function runDate($date = null)
    {
        // $date = date('H:i');
        return '2022-10-02';
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
        $shift = self::getShift();
        $query = "
        
        SELECT part_id, line, shift from production_plan where production_date='".$date."' and line is not null and shift='".$shift."' group by part_id, line 
        
        union 

        SELECT part_id, line,
        CASE 
          WHEN  FROM_UNIXTIME(created_at, \"%h:%i\")  between '08:00' and '19:59' THEN 1
        ELSE 2
        end as shift
        
        from production_order where DATE(FROM_UNIXTIME(created_at))='".$date."' and line is not null  group by part_id, line, shift
        ";
        $response = Yii::$app->db->createCommand($query)->queryAll();
        return $response;
        
    }
    public static function todayProductionPlan($part_id, $line, $shift, $date)
    {
        $shift = self::getShift();
        $query = "SELECT sum(target_qty) as qty from production_plan where part_id='".$part_id."' and line='".$line."' and shift='".$shift."' and production_date='".$date."' and target_qty > 0 and target_qty is not null";
        $response = Yii::$app->db->createCommand($query)->queryOne();
        return $response['qty']?:0;
    }
    public static function todayProductionFakt($part_id, $line, $shift, $date)
    {
        $query = "SELECT sum(quantity) as qty from production_order where part_id='".$part_id."' and line='".$line."' and DATE(FROM_UNIXTIME(created_at))='".$date."' and quantity > 0 and quantity is not null";
        $response = Yii::$app->db->createCommand($query)->queryOne();
        return $response['qty']?:0;
    }
    public static function todayProductionByData($line=null)
    {
        $date = date('Y-m-d', time());
        $partList = self::todayProductionPlanPartList($date);
        $nowTime = date('d.m.Y H:i:s', time()).' AM';
        $data['nowTime'] = $nowTime;
        $data['data'] = [];
        foreach($partList as $part){
            if(!empty($line) && $line != $part['line']){
                continue;
            }
            $data['data'][] = [
                'part_id'       => $part['part_id'],
                'part_name'     => substr(Part::getPartName($part['part_id']), 0, 25),
                'line'          => $part['line'].'-'.Yii::t('app', 'Line'),
                'lineNumber'    => $part['line'],
                'shift'         => $part['shift'].'-'.Yii::t('app', 'Shift'),
                'shiftNumber'   => $part['shift'],
                'plan'          => self::todayProductionPlan($part['part_id'], $part['line'], $part['shift'], $date)*1,
                'fakt'          => self::todayProductionFakt($part['part_id'], $part['line'], $part['shift'], $date)*1,
                'balance'       => self::todayProductionPlan($part['part_id'], $part['line'], $part['shift'], $date) - self::todayProductionFakt($part['part_id'], $part['line'], $part['shift'], $date),
            ];
        }
        return $data;
    }

    public static function getShift()
    {
        $time = date('H:i');
        if($time >= '08:00' && $time < '20:00'){
            return 1;
        }
        return 2;
    }
    public static function todayProductionByHtml($line=null)
    {
        $data = self::todayProductionByData($line);
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
            $html .= '<div class="col-md-6 text-right" style="display: flex; justify-content: space-around;align-items: center;">';
            $html .= '<span class="color-primary">'.$model['shift'].'</span>';
            $html .= '<span class="color-success form-modal" data-line="'.$model['lineNumber'].'" data-shift="'.$model['shiftNumber'].'"   data-href="/dashboard/analiz-form-modal" style="cursor: pointer;" data-partid='.$model['part_id'].'><i class="fa  fa-plus"></i></span>';
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
    /*
    
    * Bu qismdan boshhlan plan fakt va prodaj bo'yicha otchot qilingan
    * plan fakt prodaj 2023-06-16  created Sanakulov Anvar #sanakulov_dev

    */

    public static function getCustomerParts($year, $customer_id)
    {
        $query = "SELECT p.id as part_id, p.part_name, p.part_color, p.part_no FROM fg_invoice 
                inner join fg_invoice_detail fgd on fgd.fg_invoice_id=fg_invoice.id 
                inner join part p on p.part_no = fgd.part_no
                where YEAR(fg_invoice.invoice_date)='".$year."' 
                and fg_invoice.customer_id='".$customer_id."' 
                group by p.id
                
                union

                SELECT p.id as part_id, p.part_name, p.part_color, p.part_no FROM sales_plan
                inner join part p on p.id = sales_plan.part_id
                where YEAR(sales_plan.target_date)='".$year."'
                and sales_plan.customer_id='".$customer_id."'
                and sales_plan.status=1
                group by sales_plan.part_id"; 
        $parts = Yii::$app->db->createCommand($query)->queryAll();

        return $parts;
    }
    public static function getConditionParts($customer_id, $year, $firstType, $secondType)
    {
        $data = [];
        $parts = self::getCustomerParts($year, $customer_id);
        $part_ids = implode(',', ArrayHelper::getColumn($parts, 'part_id'));
        foreach($parts as $key => $part){
            $data[$part['part_id']] = [
                'part_name' => substr($part['part_name'], 0, 30),
                'part_color'=> $part['part_color'],
                'list' => self::getPartsByLists($customer_id, $part['part_id'], $year, $firstType, $secondType, $part_ids),
            ];
        }
        return $data;
    }

    public static function getPartsByLists($customer_id, $part_id, $year, $firstType, $secondType, $part_ids, $part_noes=null)
    {
        $data = [];
        if($firstType == 1){
            $months = [
                0 => '01-01',
                1 => '02-02',
                2 => '03-03',
                3 => '04-04',
                4 => '05-05',
                5 => '06-06',
                6 => '07-07',
                7 => '08-08',
                8 => '09-09',
                9 => '10-10',
                10 => '11-11',
                11 => '12-12',
            ];
        }
        elseif($firstType == 2){
            $months = [
                0 => '01-03',
                1 => '04-07',
                2 => '08-10',
                3 => '10-12', 
            ];
        }

        foreach($months as $key => $month){
            $data[$key] = [
                'plan' => self::getPlanCountsOrPrices($customer_id, $part_id, $year, $secondType, $month, $part_ids),
                'fakt' => self::getFaktCountOrPrices($customer_id, $part_id, $year, $secondType, $month, $part_ids, $part_noes),
                'balance' => self::getPlanCountsOrPrices($customer_id, $part_id, $year, $secondType, $month, $part_ids) - self::getFaktCountOrPrices($customer_id, $part_id, $year, $secondType, $month, $part_ids, $part_noes),
            ];
        }
        return $data;
    }

    public static function getPlanCountsOrPrices($customer_id, $part_id=null, $year, $secondType, $month, $part_ids=null)
    {
        $months = explode('-', $month);
        $month1 = $months[0];
        $month2 = $months[1];
        if(empty($part_ids)){
            return 0;
        }
        if($secondType == 1){
            if(empty($part_id)){
                $query = "SELECT sum(target_qty) from sales_plan where customer_id='".$customer_id."' and part_id IN(".$part_ids.")  and YEAR(target_date)='".$year."' and MONTH(target_date) between '".$month1."' and '".$month2."' and status=1";
            } else{
                $query = "SELECT sum(target_qty) from sales_plan where customer_id='".$customer_id."' and part_id='".$part_id."' and YEAR(target_date)='".$year."' and MONTH(target_date) between '".$month1."' and '".$month2."' and status=1";
            }
        }
        elseif($secondType == 2){
            if(empty($part_id)){
                $query = "SELECT sum(scd.price)  from sales_contract inner join sales_contract_detail scd on scd.sales_contract_id=sales_contract.id where sales_contract.customer_id='".$customer_id."' and scd.part_id IN(".$part_ids.") and YEAR(sales_contract.contract_date)='".$year."' and MONTH(sales_contract.contract_date) between '".$month1."' and '".$month2."' and sales_contract.status = 1";
            } else{
                $query = "SELECT sum(scd.price)  from sales_contract inner join sales_contract_detail scd on scd.sales_contract_id=sales_contract.id where sales_contract.customer_id='".$customer_id."' and scd.part_id='".$part_id."' and YEAR(sales_contract.contract_date)='".$year."' and MONTH(sales_contract.contract_date) between '".$month1."' and '".$month2."' and sales_contract.status = 1";
            }
        }
        $res = Yii::$app->db->createCommand($query)->queryScalar();
        if($secondType == 2){
            $res = $res / 1000;
        }
        return $res?round($res):0;
    }
    public static function getFaktCountOrPrices($customer_id, $part_id=null, $year, $secondType, $month, $part_ids=null, $part_noes=null)
    {
        $months = explode('-', $month);
        $month1 = $months[0];
        $month2 = $months[1];
        $part_no = Part::findOne($part_id)->part_no;
        if(empty($part_ids)){
            return 0;
        }
        $name = 'fgd.qty';
        if($secondType == 2){
            $name = 'fgd.price*fgd.qty';
        }
        if(empty($part_id)){
            $query = "SELECT sum(".$name.") from fg_invoice 
                    inner join fg_invoice_detail fgd on fgd.fg_invoice_id=fg_invoice.id 
                    inner join part p on p.part_no = fgd.part_no
                    where fg_invoice.customer_id='".$customer_id."' 
                    and p.id IN(".$part_ids.") 
                    and YEAR(fg_invoice.invoice_date)='".$year."' 
                    and MONTH(fg_invoice.invoice_date) between '".$month1."' and '".$month2."'";
        } else{
            $query = "SELECT sum(".$name.") from fg_invoice 
                    inner join fg_invoice_detail fgd on fgd.fg_invoice_id=fg_invoice.id 
                    inner join part p on p.part_no = fgd.part_no
                    where fg_invoice.customer_id='".$customer_id."' 
                    and p.id='".$part_id."' 
                    and YEAR(fg_invoice.invoice_date)='".$year."' 
                    and MONTH(fg_invoice.invoice_date) between '".$month1."' and '".$month2."'";
        }
        $res = Yii::$app->db->createCommand($query)->queryScalar();
        if($secondType == 2){
            $res = $res / 1000;
        }
        return $res?:0;
    
    }

    public static function getCustomerPlanSales($firstType =1, $secondType=1, $year)
    {
        $data = [];
        $query = "SELECT id as customer_id, name from customer  where customer_type_id=1 and status=1 order by name asc";
        $customerList = Yii::$app->db->createCommand($query)->queryAll();
        foreach($customerList as $customer){
            $parts = self::getCustomerParts($year, $customer['customer_id']);
            $part_ids = implode(',', ArrayHelper::getColumn($parts, 'part_id'));
            $part_noes = implode(',', ArrayHelper::getColumn($parts, 'part_no'));
            $data[$customer['customer_id']] = [
                'customer_name' =>  substr($customer['name'],0, 30),
                'planfaktbalance' => self::getPartsByLists($customer['customer_id'],null, $year, $firstType, $secondType, $part_ids, $part_noes),
                'parts'         => self::getConditionParts($customer['customer_id'], $year, $firstType, $secondType),
            ];
        }

        return $data;
    }
    public static function getMonths($firstType, $secondType){
        $data = [];
        if($firstType == 1){
            $months = [
                0 => 'январь',
                1 => 'февраль',
                2 => 'март',
                3 => 'апрель',
                4 => 'май',
                5 => 'июнь',
                6 => 'июль',
                7 => 'август',
                8 => 'сентябрь',
                9 => 'октябрь',
                10 => 'ноябрь',
                11 => 'декабрь',
            ];
        } elseif($firstType == 2){
            $months = [
                0 =>' 1-квартал',
                1 =>' 2-квартал',
                2 =>' 3-квартал',
                3 =>' 4-квартал',
            ];
        }
        return $months;
    }
    public static function getCustomerPlanSalesTableHeaders($firstType, $secondType)
    {
        
        $months = self::getMonths($firstType, $secondType);
        foreach($months as $key => $item){
            $data[$key]['name']     = $item;
        }

        return $data;
        
    }




    // Analiz form modal  21-06-2023

    public static function getAnalizFormModal($part_id, $line, $shift)
    {
        $data = '';
        $data = '<form action="/dashboard/analiz-form-modal" method="post" id="analiz-form">';
        $data .= '<div class="row" style="display: flex; align-items:center; justify-content:space-between;">';
        $data .= '<div class="col-md-4">';
        $data .= '<label class="form-group has-float-label">';
        $data .= '<div class="form-group field-productionplanshort-part_id required ">';
        $data .= '<label class="control-label" for="part_id">'.Yii::t('app', 'Part name').'</label>';
        $data .= '<select name="ProductionOrder[part_id]" id="part_id" class="form-control">';
        $data .= '<option selected value="'.$part_id.'">'.Part::getPartName($part_id).'</option>';
        $data .= '</select>';
        $data .= '</div>';
        $data .= '</label>';
        $data .= '</div>';
        $data .= '<div class="col-md-4">';
        $data .= '<div class="form-group required">';
        $data .= '<label class="control-label" for="quantity">'.Yii::t('app', 'Target qty').'</label>';
        $data .= '<input type="number" name="ProductionOrder[quantity]" id="quantity" class="form-control" required value="1000">';
        $data .= '</div>';
        $data .= '</div>';  
        $data .= '</div>';
        $data .= '<input name="ProductionOrder[line]" type="hidden" value="'.$line.'">';
        $data .= '<input name="ProductionOrder[shift]" type="hidden" value="'.$shift.'">';
        $data .= '</form>';

        return $data;



    }


    public static function isNollValues($model, $type = null)
    {
        $flag = true;
        foreach($model as $key => $item){
            if(empty($type)){
                if($item['plan'] != 0 || $item['fact'] != 0 || $item['balance'] != 0){
                    $flag = false;
                    break;
                }
            }
            else{
                if($item[$type] != 0){
                    $flag = false;
                    break;
                }
            }
        }
        return $flag;
    }
}
