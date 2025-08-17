<?php

namespace app\modules\api\controllers;

use app\modules\api\search\PartPartSearch as ApiBomSearch;

class BomController extends BaseController {
	public function actionIndex() {
		$search = new ApiBomSearch();
		return $search->search(\Yii::$app->request->queryParams);
	}
}