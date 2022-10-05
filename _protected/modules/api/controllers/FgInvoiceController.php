<?php

namespace app\modules\api\controllers;

use app\modules\api\search\FgInvoiceSearch as ApiFgInvoiceSearch;

class FgInvoiceController extends BaseController {
	public function actionIndex() {
		$search = new ApiFgInvoiceSearch();
		return $search->search(\Yii::$app->request->queryParams);
	}
}