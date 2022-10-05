<?php

namespace app\modules\api\controllers;

use app\modules\api\search\SupplierSearch as ApiSupplierSearch;

class SupplierController extends BaseController {
	public function actionIndex() {
		$search = new ApiSupplierSearch();
		return $search->search(\Yii::$app->request->queryParams);
	}
}