<?php

namespace app\controllers;

use app\enums\CargoType;

class CargoTypeController extends AppController
{
    public function actionIndex()
    {
        $list = CargoType::list();
        
        $data = [];
        foreach ($list as $key => $item) {
            $data[] = [
                'name' => $item,
                'desc' => CargoType::desc($key)
            ];
        }
        
        return $this->render('index',['data' => $data]);
    }

}
