<?php

namespace app\modules\api\controllers;

use app\modules\api\search\WarehouseSearch as ApiWarehouseSearch;

class WarehouseController extends BaseController {
	public function actionIndex() {
		$search = new ApiWarehouseSearch();
		return $search->search(\Yii::$app->request->queryParams);
	}
}