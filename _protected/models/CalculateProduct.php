<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use app\models\Part;
use app\models\Stock;
use app\models\ProductSpecification;
use app\models\ProductSpecificationItem;
use app\models\ContractDetail;
use app\models\Contract;
use app\models\PartOrder;
use app\models\PartOrderDetail;


class CalculateProduct extends \yii\db\ActiveRecord
{
    public $part_id;
    public $quantity;
    public $type;

    //rules
    public $customerId;
    public $fromDate;
    public $toDate;

    public function rules()
    {
        return [
            [['part_id', 'customerId'], 'integer'],
            [['quantity'], 'number'],
            [['type'], 'string', 'max' => 255],
            [['from_date', 'toDate'], 'safe']
        ];
    }

    // /labels
    public function attributeLabels()
    {
        return [
            'part_id' => 'Наименование',
            'quantity' => 'Количество',
            'type' => 'Тип',
        ];
    }
    public  static function productSpecification($part_ids, $part_ids2, $quantities)
    {
        // string to array
        $response = [];
        //explode
        // vd($arrayItemParts);
        foreach($part_ids2 as $key => $part_id){
            $partModel  = Part::find()->where(['id' => $part_id])->one();
            $stock      = Stock::find()->where(['part_id' => $part_id])->one();
            $product_specification = ProductSpecification::find()->where(['part_id' => $part_id, 'status'=>1])->one();
            $stock_qty = $stock?$stock->qty*1:0;
            $response[$key]['product_name']             = $partModel?$partModel->part_name:'';
            $response[$key]['current_stock']            = round($stock?$stock->qty*1:0, 2);
            $getItems = self::getItems($part_ids, $part_id, $stock_qty, $quantities);
            $response[$key]['avl_items']                = $getItems['avl_stocks'];
            $response[$key]['required_stock']           = $getItems['required_stock'];
            $response[$key]['balance']                  = $response[$key]['current_stock']-$response[$key]['required_stock'];
            $response[$key]['date']                     = self::leadyDate($part_id)['date'];
            $response[$key]['quantity']                 = self::leadyDate($part_id)['quantity'];
        }
        return $response;
        
    } 
    public static function specificationItems($part_ids=[])
    {
        $part_ids = array_values($part_ids);
        $query = "SELECT distinct part_id FROM product_specification_item WHERE product_specification_id IN (SELECT id FROM product_specification WHERE status=1 and part_id IN (".implode(',', $part_ids)."))";
        $data = Yii::$app->db->createCommand($query)->queryColumn();

        return $data;
    } 
    // 
    private static function getItems($part_ids, $part_id2, $stock, $quantities)
    {
        $data =[];
        $data['required_stock']=0;
        $data['avl_stocks']=[];
        foreach($part_ids as $key => $part_id){
            $product_specification = ProductSpecification::find()->where(['part_id' => $part_id, 'status'=>1])->one();
            if($product_specification && $product_specification->amount > 0){
                $product_specification_item = ProductSpecificationItem::find()->where(['product_specification_id' => $product_specification->id, 'part_id' => $part_id2])->one();
                if($product_specification_item){
                   $data['avl_stocks'][$key]['avl_stock'] = round($stock/($product_specification_item->usage_qty /$product_specification->amount), 2); 
                   $data['required_stock'] +=  round($quantities[$key]*$product_specification_item->usage_qty/$product_specification->amount,2);
                }
                else{
                    $data['avl_stocks'][$key]['avl_stock'] = 'xxx';
                }
            }
            else{
                $data['avl_stocks'][$key]['avl_stock'] = 'xxx';
            }
        }     
        return $data;   
    }
    public static  function getPartNames($part_ids)
    {
        $response = [];
        if(!empty($part_ids)){
            foreach($part_ids as $key => $part_id){
                $part = Part::findOne($part_id);
                $response[$part_id] = $part?$part->part_name:'Item'.$key;
            }
        }
        return $response;
    }
    public static function table($models, $part_ids)
    {
        
        $headerPartNames = self::getPartNames($part_ids);
        $table = '';
        $table .= '<div class="col-md-12">';
        $table .= '<table class="table table1">';
        $table .= '<thead >';
        $table .= '<tr style="text-align:center">';
        $table .= '<th rowspan="2" style="width: 100px;">
                    <div class="bg-primaries">
                        №
                    </div>
                </th>';
        $table .= '<th rowspan="2" style="width: 255px;">
                    <div class="bg-primaries">
                        '.Yii::t('app', 'Product Name').'
                    </div>
                </th>';
        $table .= '<th rowspan="2" style="width: 250px;">
                    <div class="bg-primaries">
                    '.Yii::t('app', 'Current Stock').'   
                    </div>
                </th>';
        foreach($part_ids as $key => $part_id){
            $table .= '<th rowspan="2" style="width: 200px;"><div class="bg-primaries">'.$headerPartNames[$part_id].'(Avl '.($key+1).')</div></th>';
        }
        $table .= '<th rowspan="2" style="width: 200px;"><div class="bg-primaries">'.Yii::t('app', 'Required Stock').'</div></th>';
        $table .= '<th rowspan="2"><div class="bg-primaries">'.Yii::t('app', 'Balance').'</div></th>';
        // $table .= '<th style="margin:0px; padding:0px!important;" ><div class="bg-primaries" style="margin:0px!important;">'.Yii::t('app', 'Next Arrival').'</div></th> ';
        $table .= '<th><div class="bg-primaries">'.Yii::t('app', 'Date').'</div></th>';
        $table .= '<th><div class="bg-primaries">'.Yii::t('app', 'Quantity').'</div</th>';
        $table .= '</tr>';
        // $table .= '<tr>';
        // $table .= '<th></th>';
        // $table .= '<th></th>';
        // $table .= '<th></th>';
        // $table .= '<th></th>';
        // $table .= '<th></th>';
        // $table .= '<th></th>';
        // $table .= '</tr>';

        $table .= '</thead>';
        $table .= '<tbody>';
        foreach($models as $key => $item){
            $style = '';
            if($item['balance'] < 0){
                $style = 'style="background-color:#faa2a2"';
            }
            else{
                $style = 'style="background-color:lightgreen"';
            }
            $table .= '<tr>';
            $table .= '<td><div class="bg-lighties">'.($key+1).'</div></td>';
            $table .= '<td><div class="bg-lighties">'.$item['product_name'].'</div></td>';
            $table .= '<td><div class="bg-lighties">'.$item['current_stock'].'</div></td>';
            if(!empty($item['avl_items'])){
                foreach($item['avl_items'] as $key2 => $avl_item){
                    if($avl_item['avl_stock'] === 'xxx'){
                        $table .= '<td><div style="background-color:#faa2a2" class="bg-lighties">X</div></td>';
                    }
                    else{
                        $table .= '<td><div class="bg-lighties">'.$avl_item['avl_stock'].'</div></td>';
                    }
                }
            }
            else{
                $table .= '<td><div class="bg-lighties">0</div></td>';
            }
            $styleDate1 ='';
            $styleDate2 ='';
            if(empty($item['date'])){
                $styleDate1 ='style="background-color:#faa2a2"' ;
                $item['date'] = 'X';
            }
            if(empty($item['quantity'])){
                $styleDate2 ='style="background-color:#faa2a2"' ;
                $item['quantity'] = 'X';
            }
            $table .= '<td><div class="bg-lighties">'.$item['required_stock'].'</div></td>';
            $table .= '<td><div class="bg-lighties" '.$style.'>'.$item['balance'].'</div></td>';
            $table .= '<td><div class="bg-lighties" '.$styleDate1.'>'.$item['date'].'</div></td>';
            $table .= '<td><div class="bg-lighties" '.$styleDate2.'>'.$item['quantity'].'</div></td>';
            
        }
        $table .= '</tbody>';   
        $table .= '</table>'; 
        $table .= '</div>';  
        return $table;  
    }


    //eng kichik avl
    public static function minimumProductAvl($part_id)
    {
        $data=[];
        $product_specification = ProductSpecification::find()->where(['part_id' => $part_id, 'status'=>1])->one();
        if($product_specification && $product_specification->amount > 0){
            $product_specification_items = ProductSpecificationItem::find()->where(['product_specification_id' => $product_specification->id])->all();
            foreach($product_specification_items as $key => $product_specification_item){
                $stock = Stock::find()->where(['part_id' => $product_specification_item->part_id])->one();
                if($stock && $product_specification_item){
                    $data[] = round($stock->qty/($product_specification_item->usage_qty /$product_specification->amount), 2); 
                }
            }
        }
        return empty($data)?0:min($data);
    }

    // add-item-table
    public  static function addItemTable($part_ids=[], $quantities=[])
    {
        $table = '';
        if(!empty($part_ids) && !empty($quantities) && count($part_ids) == count($quantities)) {
            $table .='<h2 class="text-uppercase" style="font-weight: bold;">'.Yii::t('app', 'Availability for shipment').'</h2>';
            $table .='<div class="col-md-10">';
            $table .='<table class="table table1">';
            $table .='<thead>';
            $table .='<tr>';
            $table .='<th style="width: 100px;"><div class="bg-primaries">№</div></th>';
            $table .='<th style="width: 255px;"><div class="bg-primaries">'.Yii::t('app', 'Product Name').'</div></th>';
            $table .='<th><div class="bg-primaries">'.Yii::t('app', 'Quantity').'</div></th>';
            $table .='<th><div class="bg-primaries">'.Yii::t('app', 'AVL').'</div></th>';
            $table .='<th><div class="bg-primaries">'.Yii::t('app', 'Balance').'</div></th>';
            $table .= '<th></th>';
            $table .= '<th></th>';
            $table .= '<th></th>';
            $table .= '<th></th>';
            $table .='</tr>';
            $table .='</thead>';
            $table .='<tbody>';
            foreach($part_ids as $key=>$part_id){
                $model = Part::findOne($part_id);
                $table .='<tr>';
                $avl = self::minimumProductAvl($part_id);
                $balance = $quantities[$key]-$avl;
                $style = '';
                if($balance < 0){
                    $style = 'style="background-color:#faa2a2"';
                }
                else{
                    $style = 'style="background-color:lightgreen"';
                }
                $table .='<td><div class="bg-lighties">'.($key+1).'</div></td>';
                $table .='<td><div class="bg-lighties">'.$model->part_name.' '.$model->part_no.'</div></td>';
                $table .='<td><div class="bg-lighties">'.$quantities[$key].'</div></td>';
                $table .='<td><div class="bg-lighties">'.$avl.  '</div></td>';
                $table .='<td><div class="bg-lighties" '.$style.'>'.$balance.'</div></td>';
                $table .='<td></td>';
                $table .='<td></td>';
                $table .='<td></td>';
                $table .='<td></td>';
                $table .='</tr>';
            }
            $table .='</tbody>';
            $table .='</table>';
            $table .='</div>';

        }
        return $table;
    }

    //contract-detail leady time
    public static function leadyDate($part_id)
    {
        $todayDate = date('d.m.Y', time());
        $todayTime = time();
        $data['date']         = null;
        $data['quantity']     = null;
        if($part_id){
            $invoice_detail = InvoiceDetail::find()->where(['part_id' => $part_id])->orderBy(['id'=>SORT_DESC])->one();
            if($invoice_detail){
                $containerInvoice = ContainerInvoice::findOne($invoice_detail->cont_inv_id);
                if($containerInvoice){
                    $date = date('d.m.Y',strtotime($containerInvoice->app_arr_at));
                    $dateTime = strtotime($containerInvoice->app_arr_at);
                    if($dateTime >= $todayTime){
                        $data['date']         = $date;
                        $data['quantity']     = $invoice_detail->qty*1;
                    }
                   
                }
               
            }
            
        }
        return $data;
    }
    /**
     * Anvar Sanakulov
     * 2023-11-19
     * @sanakulov_Dev
     * 
     */
    public static function customers($customerId, $from, $to, $term=1)
    {
        $query = "SELECT 
            fgt.part_name, fgt.part_no, fgt.qty, fgt.price, fg.invoice_no, fg.invoice_date, fg.contract, fg.contract_date,
            w.waybill_no, w.waybill_date, ct.name
          from fg_invoice  fg
          inner join fg_invoice_detail fgt on fgt.fg_invoice_id = fg.id
          inner join fg_invoice_waybill fgw on fgw.fg_invoice_id = fg.id
          inner join customer ct on fg.customer_id=ct.id
          inner join waybill w on fgw.waybill_id=w.id
          where ct.id=$customerId  and fg.invoice_date between '$from' and '$to'
          order by fg.invoice_date desc
          ";

        $res = Yii::$app->db->createCommand($query)->queryAll();
        $res2 = Yii::$app->db->createCommand($query)->getRawSql();
        // vd($res2);
        return $res;
    }
}