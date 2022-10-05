<?php

namespace app\modules\api\controllers;

use app\services\ReportService;

class ReportController extends BaseController {
	public $_service;
	
	public function init()
	{
		$this->_service = new ReportService();
	}

	public function actionCoverageByVehicleSet() {
		return $this->_service->coverageByVehicleSet();
	}

	public function actionStock() {
		return $this->_service->stock();
	}
}