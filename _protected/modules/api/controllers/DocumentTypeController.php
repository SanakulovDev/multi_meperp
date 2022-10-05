<?php

namespace app\modules\api\controllers;

use app\modules\api\search\DocumentTypeSearch as ApiDocumentTypeSearch;

class DocumentTypeController extends BaseController {
	public function actionIndex() {
		$search = new ApiDocumentTypeSearch();
		return $search->search(\Yii::$app->request->queryParams);
	}
}