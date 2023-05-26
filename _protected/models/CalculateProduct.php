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
            $response[$key]['product_name']             = $partModel?$partModel->part_name:'';
            $response[$key]['current_stock']            = round($stock?$stock->qty*1:0, 2);
            $response[$key]['end_week_stock']           = 0;
            $items = self::getItems($part_ids, $part_id,  $stock?$stock->qty*1:1);
            $response[$key]['items'] = $items;
          
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
    private static function getItems($part_ids, $part_id2, $stock)
    {
        $data = [];
        foreach($part_ids as $key => $part_id){
            $product_specification = ProductSpecification::find()->where(['part_id' => $part_id, 'status'=>1])->one();
            if($product_specification && $product_specification->amount > 0){
                $avg = $stock/$product_specification->amount*1;
                $product_specification_item = ProductSpecificationItem::find()->where(['product_specification_id' => $product_specification->id, 'part_id' => $part_id2])->one();
                if($product_specification_item){
                    // return $product_specification_item;
                    $data[$part_id] = round($product_specification_item->usage_qty*1*$avg, 2);
                }
                else{
                    $data[$part_id] = 0;
                }
            }
            else{
                $data[$part_id] = 0;
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
        $table .= '<table class="table">';
        $table .= '<thead>';
        $table .= '<tr>';
        $table .= '<th>№</th>';
        $table .= '<th>Product Name</th>';
        $table .= '<th>Current Stock</th>';
        $table .= '<th>End Wk Stock</th>';
        foreach($headerPartNames as $key => $partName){
            $table .= '<th>'.$partName.'</th>';
        }
        $table .= '<th>Next arrival</th>';
        $table .= '</tr>';
        $table .= '</thead>';
        $table .= '<tbody>';
        foreach($models as $key => $item){
            $table .= '<tr>';
            $table .= '<td>'.($key+1).'</td>';
            $table .= '<td>'.$item['product_name'].'</td>';
            $table .= '<td>'.$item['current_stock'].'</td>';
            $table .= '<td>'.$item['end_week_stock'].'</td>';
            if(!empty($item['items'])){
                foreach($item['items'] as $key2 => $item2){
                    $table .= '<td>'.$item2.'</td>';
                }
            }
            else{
                foreach($headerPartNames as $key => $partName){
                    $table .= '<td>0</td>';
                }
            }
            $table .= '<td></td>';
        }
        $table .= '</tbody>';   
        $table .= '</table>';   
        return $table;  
    }


}