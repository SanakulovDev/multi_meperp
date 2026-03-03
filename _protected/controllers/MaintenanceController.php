<?php
	namespace app\controllers;

	class MaintenanceController extends Controller{

		public function actionEnable(){
			Yii::$app->maintenance->enable();
		}

		public function actionDisable(){
			Yii::$app->maintenance->disable();
		}

	}
