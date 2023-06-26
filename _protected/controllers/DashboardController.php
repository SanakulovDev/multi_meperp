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

    public function actionDownloadIndex($date = null)
    {
        if(empty($date)){
            $date = Dashbard::rundate();
        }
        else{
            $date = date('Y-m-d', strtotime($date));
        }
        $headerTitles = [
            'ПРОИЗВЕДЕНО',
            'ОТГРУЖЕНО',
            'ПРИХОД',
            'НОРМА РАСХОДА'
        ];
        $models1 = Dashboard::fakt($date);
        $models2 = Dashboard::ttn($date);
        $models3 = Dashboard::prixod($date);
        // $models4 = Dashboard::
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
                    $model->quantity_of_copy    = 1;
                    $model->current_event       = ProductionOrder::EVENT_PRODUCED;
                    $model->current_seq         = $model->getCurrentSeq($model->part_id) + 1;
                    $model->is_printed          = 0;
                    $model->is_label            = 2;
                    $model->created_by          = Yii::$app->user->id;
                    $model->created_at          = time();
                    $model->updated_at          = time();
                    $spec = ProductSpecification::find()
                    ->where(["part_id" => $model->part_id, "status" => ProductSpecification::STATUS_ACTIVE])
                    ->one();
                    $model->product_specification_id = $spec?$spec->id:null;
                    $model->serial_number = $model->generateSerialNumber();
                    // vd($model);
                    if($model->save(false)){
                        $data['status'] = 1;
                        $data['message'] = 'Success';
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        return $data;
                    }
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

    public function actionPlanProdajNew($firstType=1, $secondType=2, $year = null)
    {
        $years = [
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







    // rezultat prodaj oylik 2023
    // ============================================
    // =============Sanakulov Anvar================
    // ============================================
    // 2023-06-25  Sanakulov Anvar
    public function actionReportPlanMonth($month = null, $year = null)
    {
        if($month == null){
            $month = date('m');
        }
        if($year == null){
            $year = date('Y');
        }
        
        $monthList = [
            '01' => 'Январь',
            '02' => 'Февраль',
            '03' => 'Март',
            '04' => 'Апрель',
            '05' => 'Май',
            '06' => 'Июнь',
            '07' => 'Июль',
            '08' => 'Август',
            '09' => 'Сентябрь',
            '10' => 'Октябрь',
            '11' => 'Ноябрь',
            '12' => 'Декабрь',
        ];
        $year = '2021';
        $month = '07';
        $monthName = $monthList[$month];
        $models = ReportFaktProdajMonth::resultReport($month, $year);
        // vd($models);
        $headers = '';
        // $models = Dashboard::getMonthResult($month, $year);
        // $headers = Dashboard::getMonthResultTableHeaders();
        return $this->render('report-plan-month', [
            'models'        => $models,
            'headers'       => $headers,
            'month'         => $month,
            'year'          => $year,
            'monthName'     => $monthName,
            'monthList'     => $monthList,
        ]);
    }
}
