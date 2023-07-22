<?php

namespace app\controllers;
use Yii;
use app\models\Dashboard;
use app\models\ProductionOrder;
use app\models\ProductSpecification;
use app\models\ReportFaktProdajMonth;
use yii\web\Response;

class DashboardController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    // fakt
    public function actionFakt()
    {
        $post = Yii::$app->request->post();
        if($post && isset($post['date'])){
            $title = 'Произведено';
            $date = date('Y-m-d', strtotime($post['date']));
            $models = Dashboard::fakt($date);
            if(empty($models)){
                $data = '<div class="col-md-12">';
                $data .= '<h4 class="text-uppercase " style="font-weight: bold">'.$title.'</h4>';
                $data .= '<p>Нет данных</p>';
                $data .= '</div>';
                return json_encode([
                    'id' => 1,
                    'html' => $data,
                ]);
            }
            $data = Dashboard::shablon($title, $models, 'fakt');
            return json_encode([
                'id' => 1,
                'html' => $data,
            ]);
        }
        return $this->render('fakt');
    }
    // ttn
    public function actionTtn()
    {
        $post = Yii::$app->request->post();
        if($post && isset($post['date'])){
            $date = date('Y-m-d', strtotime($post['date']));
            $models = Dashboard::ttn($date);
            $title = 'Отгружено';
            if(empty($models)){
                $data = '<div class="col-md-12">';
                $data .= '<h4 class="text-uppercase font-weight-bold" style="font-weight: bold">'.$title.'</h4>';
                $data .= '<p>Нет данных</p>';
                $data .= '</div>';
                return json_encode([
                    'id' => 2,
                    'html' => $data,
                ]);
            }
            $data = Dashboard::shablon($title, $models, 'ttn');
            return json_encode([
                'id' => 2,
                'html' => $data,
            ]);
        }
        return $this->render('ttn');
    }
    // prixod
    public function actionPrixod()
    {
        $post = Yii::$app->request->post();
        if($post && isset($post['date'])){
            $date = date('Y-m-d', strtotime($post['date']));
            $models = Dashboard::prixod($date);
            $title = 'Приход';
            if(empty($models)){
                $data = '<div class="col-md-12">';
                $data .= '<h4 class="text-uppercase" style="font-weight: bold">'.$title.'</h4>';
                $data .= '<p>Нет данных</p>';
                $data .= '</div>';
                return json_encode([
                    'id' => 3,
                    'html' => $data,
                ]);
            }
            $data = Dashboard::shablon($title, $models, 'prixod');
            return json_encode([
                'id' => 3,
                'html' => $data,
            ]);
        }
        return $this->render('prixod');
    }
    // norma rasxoda
    public function actionNormaRasxod()
    {
        $post = Yii::$app->request->post();
        if($post && isset($post['date'])){
            $date = date('Y-m-d', strtotime($post['date']));
            $items = Dashboard::fakt($date);
            $data = '';
            if($items){
                foreach($items as $item){
                    $color = isset($item['part_color']) ? $item['part_color'] : '';
                    $title = $item['part_name'].'('.$color.')';
                    $models = Dashboard::normaRasxoda($item['part_id'], $post['date'], $item['quantity']*1);
                    if(!empty($models)){
                        $data .= Dashboard::shablon($title, $models, 'norma-rasxod');
                    }
                }
                return json_encode([
                    'id' => 4,
                    'html' => $data,
                ]);
            }
            return json_encode([
                'id' => 4,
                'html' => '',
            ]);
        }
        return $this->render('norma-rasxoda');
    }


    // action analiz
    public function actionAnaliz($line=null)
    {
       $this->layout='req';
       $lines = ProductionOrder::getLines();
       $lines = array_merge([0 => 'Все'], $lines);
        return $this->render('analiz', [
            'lines' => $lines,
            'term'  => $line,
        ]);
    }   

    public function actionAnalizAjax()
    {
        if(Yii::$app->request->isAjax){
            $post = Yii::$app->request->post();
            $line = null;
            if(isset($post['line'])){
                $line = $post['line'];
            }
            $nowTime = date('d.m.Y H:i:s', time()).' AM';
            $data = Dashboard::todayProductionByHtml($line);
            // vd($data);
            return json_encode([
                'nowTime'   => $nowTime,
                'html'      => $data,
            ]);
        }
    }

    public function actionAnalizFormModal($part_id=null,$line=null, $shift=null)
    {
        if(Yii::$app->request->isAjax){
            if(Yii::$app->request->post()){
                $model = new ProductionOrder();
                $post = Yii::$app->request->post();
                // $post = (object) $post;
                if($model->load($post)){
                  $post['ProductionOrder']['quantity_of_copy']    = 1;
                  $post['produced']                               = 1;
                  $post['model']                                  = 1;
                  $post['side']                                   = 'LH';
                  $post['floc']                                   = 1;

                  // return json_encode($post['ProductionOrder']);
                    $spec = ProductSpecification::find()
                    ->where(["part_id" => $model->part_id, "status" => ProductSpecification::STATUS_ACTIVE])
                    ->one();
                    $model->product_specification_id = $spec?$spec->id:null;
                    $model->serial_number = $model->generateSerialNumber();

                      $xozir_time = date("H:i");
                      $shift = Yii::$app->params["shifts"];
                      $shift_1 = $shift["1"]["0"];
                      $shift_2 = $shift["2"]["0"]["0"];
                      $shift_1_p1 = date("H:i", strtotime($shift_1) + 60 * 60);
                      $shift_1_m1 = date("H:i", strtotime($shift_1) - 1);
                      $shift_2_p1 = date("H:i", strtotime($shift_2) + 60 * 60);
                      $shift_2_m1 = date("H:i", strtotime($shift_2) - 1);
                      $prev_shift = 0;
                      $shift_crt_at = time();
                      if ($xozir_time >= $shift_1 && $xozir_time < $shift_1_p1) {
                        $prev_shift = 1;
                        $shift_crt_at = strtotime(date("Ymd") . $shift_1_m1);
                      } elseif ($xozir_time >= $shift_2 && $xozir_time < $shift_2_p1) {
                        $prev_shift = 1;
                        $shift_crt_at = strtotime(date("Ymd") . $shift_2_m1);
                      }

                        $crtResult = ProductionOrder::createProdOrders($post, $shift_crt_at);
                        $data['status'] = 1;
                        $data['message'] = 'Success';
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        return $data;
                }
            }

            return Dashboard::getAnalizFormModal($part_id, $line, $shift);
        }
    }
    // 16-06-2023 Sanakulov Anvar  bu qismda Plan Prodaj qismi bo'ladi
    public function actionPlanProdaj($firstType=1, $secondType=1, $year = null)
    {
        if($year == null){
            $year = date('Y');
            // $year = 2021;
        }
        $models = Dashboard::getCustomerPlanSales($firstType, $secondType, $year);
        // vd($models);
        $headers = Dashboard::getCustomerPlanSalesTableHeaders($firstType, $secondType);
        return $this->render('plan-prodaj', [
            'models'        => $models,
            'headers'       => $headers,
            'firstType'     => $firstType,
            'secondType'    => $secondType,
        ]);
    }

    public function actionPlanProdajNew($firstType=1, $secondType=1, $year = null)
    {
        $years = [
            2020 => 2020,   
            2021 => 2021,
            2022 => 2022,
            2023 => 2023,
            2024 => 2024,
            2025 => 2025,
        ];
        
        if($year == null){
            $year = date('Y');
            // $year = 2021;
        }
        $models = Dashboard::getCustomerPlanSales($firstType, $secondType, $year);
        // vd($models);
        $headers = Dashboard::getCustomerPlanSalesTableHeaders($firstType, $secondType);
        return $this->render('plan-prodaj-new', [
            'models'        => $models,
            'headers'       => $headers,
            'firstType'     => $firstType,
            'secondType'    => $secondType,
            'years'         => $years,
            'year'          => $year,
        ]);
    }







}
