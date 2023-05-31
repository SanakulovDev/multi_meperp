<?php

namespace app\controllers;
use Yii;
use app\models\CalculateProduct;
use app\models\PechatProduct;
use app\models\Stock;
class CalculateProductController extends \yii\web\Controller
{
    public function actionIndex()
    {
        for($i=0;$i<1; $i++){
            $models[] = new CalculateProduct;
            $models[$i]->type = 'test';
            $models[$i]->quantity = null;
            $models[$i]->part_id = 0;
        }
        return $this->render('index', [
            'models' =>  $models
        ]);
    }



    public function actionForm()
    {
        for($i=0;$i<5; $i++){
            $models[] = new CalculateProduct;
            $models[$i]->type = 'test';
            $models[$i]->quantity = 0;
            $models[$i]->part_id = 0;
        }
        return $this->render('_form', [
            'models' => (empty($models)) ? [new CalculateProduct] : $models
        ]);
    }

    // yangi product qo'shish
    public function actionNewProduct()
    {
        $post = Yii::$app->request->post();
        
        $partlist = PechatProduct::getPartsList();
        if($post && Yii::$app->request->isAjax){
            $id = $post['lastId'];
            $data = '';
            $data .= '<tr class="item-'.$id.'">';    
            $data .= '<td><div class="bg-lighties">'.($id+1).'</div></td>';
            $data .= '<td>';
            // form group
            $data .= '<div class="form-group field-calculateproduct-'.$id.'-type">';
            $data .= '<select class="select2 form-control part_id" data-id="'.$id.'" name="CalculateProduct['.$id.'][part_id]" id="calculateproduct-'.$id.'-part_id">';
            $data .= '<option value="">---</option>';
            foreach($partlist as $key => $value){
                $data .= '<option value="'.$key.'">'.$value.'</option>';
            }
            $data .= '</select>';
            $data .= '</div>';
            $data .= '</td>';
            $data .= '<td>';
            // form  group
            $data .= '<div class="form-group field-calculateproduct-'.$id.'-quantity">';
            $data .= '<input type="number" data-id="'.$id.'" class="form-control quantity text-right" placeholder="0" name="CalculateProduct['.$id.'][quantity]" id="calculateproduct-'.$id.'-quantity">';
            $data .= '</div>';
            $data .= '</td>';
            $data .= '<td class="text-center">';
            $data .= '<button style="border: 2px solid black;" class="btn btn-danger text-center remove-product-item" data-id="'.$id.'"><i class="fa fa-trash"></i></button>';
            $data .= '</td>';
            $data .= '</tr>';
            return $data;
        }
    }

    // calculate product
    public function actionGetProductOstatok()
    {
        //norma rasxoda stok index

        $post = Yii::$app->request->post();
        if($post && isset($post['part_id'])){
            $part_id = $post['part_id'];
            $avl = CalculateProduct::minimumProductAvl($part_id);
            return json_encode($avl);
        }
        return 0;
    }

    // haqiqiy hisob kitob ishlari
    public function actionReportTable()
    {
        $post = Yii::$app->request->post();
        if($post && isset($post['part_ids']) && isset($post['quantities'])){
            $part_ids   = json_decode($post['part_ids']);
            $quantities = json_decode($post['quantities']);  
            $part_ids2  = CalculateProduct::specificationItems($part_ids);
            $data       = CalculateProduct::productSpecification($part_ids, $part_ids2,      $quantities);
            // vd($data);
            $response   = CalculateProduct::table($data, $part_ids);

            $response2 = CalculateProduct::addItemTable($part_ids, $quantities);
            return json_encode([
                'data1' => $response2,
                'data2' => $response,
            ]);
        }
    }

    
}
