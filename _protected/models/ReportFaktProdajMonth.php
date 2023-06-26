<?php

namespace app\models;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use Yii;

class ReportFaktProdajMonth extends \yii\base\Model{


    public  function test()
    {
        return 'test';

    }
    // oy bo'yicha summalardan tuzilgan plan
    public static function resultReport($month, $year)
    {
        $data = [];
        $customers = self::customers();
        foreach($customers as $customer){
            $customer_id    = $customer['customer_id'];
            $customer_name  = $customer['name'];
            $parts          = self::getCustomerParts($year, $month, $customer_id);
            $partIds        = ArrayHelper::getColumn($parts, 'part_id');
            $partIds        = implode(',', $partIds);
            $data[$customer_id]['customer_name']    = $customer_name;
            $data[$customer_id]['customer_id']      = $customer_id;
            $data[$customer_id]['plan']             = self::getPlan(1, $customer_id, $year, $month, $partIds);
            $data[$customer_id]['fakt']             = self::getFakt(1, $customer_id, $year, $month, $partIds);
            $data[$customer_id]['balance']          = self::getDiff(1, $customer_id, $year, $month, $partIds);
            $data[$customer_id]['parts']            = self::getConditionParts($customer_id, $year, $month);
        }

        return $data;
    }

    public static function customers()
    {
        $query = "SELECT id as customer_id, name from customer  where customer_type_id=1 and status=1 order by id asc";
        $customers = \Yii::$app->db->createCommand($query)->queryAll();
        return $customers;
    }

    public static function getCustomerParts($year, $month, $customer_id)
    {
        $query = "SELECT p.id as part_id, p.part_name, p.part_color, p.part_no FROM fg_invoice 
                    inner join fg_invoice_detail fgd on fgd.fg_invoice_id=fg_invoice.id 
                    inner join part p on p.part_no = fgd.part_no
                    where YEAR(fg_invoice.invoice_date)='".$year."' 
                    and MONTH(fg_invoice.invoice_date)='".$month."'
                    and fg_invoice.customer_id='".$customer_id."' 
                    group by p.id
                    
                    union

                    SELECT p.id as part_id, p.part_name, p.part_color, p.part_no FROM sales_plan
                    inner join part p on p.id = sales_plan.part_id
                    where YEAR(sales_plan.target_date)='".$year."'
                    and MONTH(sales_plan.target_date)='".$month."'
                    and sales_plan.customer_id='".$customer_id."'
                    group by sales_plan.part_id
                    "; 
        $parts = Yii::$app->db->createCommand($query)->queryAll();
        // vd($parts);
        return $parts;
    }
    public static function getConditionParts($customer_id, $year, $month)
    {

        $data = [];
        $parts = self::getCustomerParts($year, $month, $customer_id);
        foreach($parts as $key => $part){
            $data[$key]['part_id']      = $part['part_id'];
            $data[$key]['part_name']    = $part['part_name'];
            $data[$key]['part_color']   = $part['part_color'];
            $data[$key]['part_no']      = $part['part_no'];
            $data[$key]['plan']         = self::getPlan($part['part_id'], $customer_id, $year, $month);
            $data[$key]['fakt']         = self::getFakt($part['part_id'], $customer_id, $year, $month);
            $data[$key]['balance']         = self::getDiff($part['part_id'], $customer_id, $year, $month);
        }
        return $data;
    }

    public static function getPlan($part_id, $customer_id, $year, $month, $partIds=null)
    {
        $data['quantity']   = 0;
        $data['price']      = 0;
        $data['sum']        = 0;
        if(!empty($partIds)){
            $condition = "scd.part_id in (".$partIds.")";
        }
        else{
            $condition = "scd.part_id='".$part_id."' ";
        }
        
        $queryQty = "SELECT sum(scd.target_qty) as quantity FROM sales_plan scd
                    where $condition
                    and YEAR(scd.target_date)='".$year."' 
                    and MONTH(scd.target_date)='".$month."'
                    and scd.status=1 and scd.customer_id='".$customer_id."'
                    
                    ";
        $planQty = Yii::$app->db->createCommand($queryQty)->queryOne();
        $data['quantity'] = $planQty['quantity']?:0;
        if($data['quantity'] == 0){
            return $data;
        }
        $queryDescPrice = "SELECT scd.price as price FROM sales_contract_detail scd
                        inner join sales_contract sc on sc.id=scd.sales_contract_id
                        where $condition
                        and YEAR(sc.contract_date)='".$year."' 
                        and MONTH(sc.contract_date)='".$month."'
                        and sc.status=1 and sc.customer_id='".$customer_id."'
                        order by sc.contract_date desc
                        limit 1
                        ";
        $planDescPrice = Yii::$app->db->createCommand($queryDescPrice)->queryOne();

        $data['price'] = $planDescPrice['price']?:0;
        $data['sum'] = $planDescPrice['price'] * $planQty['quantity'];
        
        return $data;
    }

    public static function getFakt($part_id, $customer_id, $year, $month, $partIds=null)
    {
        $data['quantity']   = 0;
        $data['price']      = 0;
        $data['sum']        = 0;
        $condition          = "";
        if(!empty($partIds)){
            $condition = "  p.id in (".$partIds.")";
            // vd($partIds);
        }
        else{
            $condition = "  p.id='".$part_id."' ";
        }
        $queryQty = "SELECT sum(fgd.qty) as quantity FROM fg_invoice_detail fgd
                    inner join fg_invoice fg on fg.id=fgd.fg_invoice_id
                    left join part p on p.part_no = fgd.part_no
                    where $condition
                    and YEAR(fg.invoice_date)='".$year."' 
                    and MONTH(fg.invoice_date)='".$month."'
                    and fg.customer_id='".$customer_id."'
                    and fg.confirmed_by is not null
                    ";

        $faktQty = Yii::$app->db->createCommand($queryQty)->queryOne();

        $data['quantity'] = $faktQty['quantity']?:0;
        if($data['quantity'] == 0){
            return $data;
        }
        $queryDescPrice = "SELECT fgd.price as price FROM fg_invoice_detail fgd
                        inner join fg_invoice fg on fg.id=fgd.fg_invoice_id
                        left join part p on p.part_no = fgd.part_no
                        where $condition
                        and YEAR(fg.invoice_date)='".$year."' 
                        and MONTH(fg.invoice_date)='".$month."'
                        and fg.customer_id='".$customer_id."'
                        order by fg.invoice_date desc
                        limit 1
                        ";
        $faktDescPrice = Yii::$app->db->createCommand($queryDescPrice)->queryOne();
        $data['price'] = $faktDescPrice['price']?:0;
        $data['sum'] = $faktDescPrice['price'] * $faktQty['quantity'];

        return $data;
        
    }

    public static function getDiff($part_id, $customer_id, $year, $month, $partIds=null)
    {
        $data = [];
        return [
            'quantity'  => self::getPlan($part_id, $customer_id, $year, $month, $partIds)['quantity'] - self::getFakt($part_id, $customer_id, $year, $month, $partIds)['quantity'],    
            'price'     => self::getPlan($part_id, $customer_id, $year, $month, $partIds)['price'] - self::getFakt($part_id, $customer_id, $year, $month, $partIds)['price'],
            'sum'       => self::getPlan($part_id, $customer_id, $year, $month, $partIds)['sum'] - self::getFakt($part_id, $customer_id, $year, $month, $partIds)['sum']
        ];
        
    }
}