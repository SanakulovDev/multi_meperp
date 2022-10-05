<?php

namespace app\controllers;

use Adldap\Models\Container;
use app\enums\ContainerType;

class ContainerTypeController extends AppController
{
  public function actionIndex()
  {
    $list = ContainerType::list();

    $data = [];
    foreach ($list as $key => $item) {
      $data[] = [
        "name" => $item,
        "capacity" => ContainerType::$capacity[$key],
        "load" => ContainerType::$load[$key],
      ];
    }

    return $this->render("index", ["data" => $data]);
  }
}
