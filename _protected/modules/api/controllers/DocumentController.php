<?php

namespace app\modules\api\controllers;

use app\modules\api\search\DocumentSearch as ApiDocumentSearch;

class DocumentController extends BaseController {
	public function actionIndex() {
		$search = new ApiDocumentSearch();
		return $search->search(\Yii::$app->request->queryParams);
	}
}