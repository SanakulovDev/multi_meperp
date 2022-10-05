<?php

namespace app\modules\api\controllers;

use app\modules\api\search\PartSearch as ApiPartSearch;

class PartController extends BaseController {
	public function actionIndex() {
		$search = new ApiPartSearch();
		return $search->search(\Yii::$app->request->queryParams);
	}
}