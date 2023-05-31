<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use app\models\Part;
use app\models\Stock;
use app\models\ProductSpecification;


class CalculateProduct extends \yii\db\ActiveRecord
{
    public $part_id;
    public $quantity;
    public $type;

    //rules
    public function rules()
    {
        return [
            [['part_id', 'quantity'], 'required'],
            [['part_id'], 'integer'],
            [['quantity'], 'number'],
            [['type'], 'string', 'max' => 255],
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
                    $data['avl_stocks'][$key]['avl_stock'] = 0;
                }
            }
            else{
                $data['avl_stocks'][$key]['avl_stock'] = 'x';
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
        // modelsni aylantirib jadval ko'rinishiga olib kelamiz
        // part_ids -bu hamma mahsulot uchun umumiy bo'ladigan product_specification itemdan olingan part_id lar
        $headerPartNames = self::getPartNames($part_ids);
        $table = '';
        $table .= '<div class="row">';
        $table .= '<div class="col-md-10" style="border: 1.5px solid black; padding: 20px; margin: 20px;">';
        $table .= '<div class="table-responsive">';
        $table .= '';
        $table .= '<table class="table">';
        $table .= '<thead >';
        $table .= '<tr style="text-align:center">';
        $table .= '<th>№</th>';
        $table .= '<th>Product Name</th>';
        $table .= '<th>Current Stock</th>';
        foreach($part_ids as $key => $part_id){
            $table .= '<th>'.$headerPartNames[$part_id].'(Avl '.($key+1).')</th>';
        }
        $table .= '<th>Required Stock</th>';
        $table .= '<th>Balance</th>';
        $table .= '</tr>';
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
            $table .= '<td>'.($key+1).'</td>';
            $table .= '<td>'.$item['product_name'].'</td>';
            $table .= '<td>'.$item['current_stock'].'</td>';
            if(!empty($item['avl_items'])){
                foreach($item['avl_items'] as $key2 => $avl_item){
                    if($avl_item['avl_stock'] == 'x'){
                        $table .= '<td style="background-color:#faa2a2">X</td>';
                    }
                    else{
                        $table .= '<td>'.$avl_item['avl_stock'].'</td>';
                    }
                }
            }
            else{
                $table .= '<td>0</td>';
            }
            $table .= '<td>'.$item['required_stock'].'</td>';
            $table .= '<td '.$style.'>'.$item['balance'].'</td>';
            
        }
        $table .= '</tbody>';   
        $table .= '</table>';   
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

}