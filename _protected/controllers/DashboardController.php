<?php

namespace app\controllers;
use Yii;
use app\models\Dashboard;

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
            $models = Dashboard::fakt($post['date']);
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
            $data = Dashboard::shablon($title, $models);
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
            $models = Dashboard::ttn($post['date']);
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
            $data = Dashboard::shablon($title, $models);
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
            $models = Dashboard::prixod($post['date']);
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
            $data = Dashboard::shablon($title, $models);
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
            $items = Dashboard::productionPlan($post['date']);
            $data = '';
            if($items){
                foreach($items as $item){
                    $color = isset($item['part_color']) ? $item['part_color'] : '';
                    $title = $item['part_name'].'('.$color.')';
                    // vd($item);
                    $models = Dashboard::normaRasxoda($item['part_id'], $post['date'], $item['quantity']);
                    if(!empty($models)){
                        $data .= Dashboard::shablon($title, $models);
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
}
