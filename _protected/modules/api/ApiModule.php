<?php
namespace app\modules\api;

use Yii;
use yii\web\ErrorHandler;
use yii\web\Response;

class ApiModule extends \yii\base\Module{
	public $controllerNamespace = 'app\modules\api\controllers';

	public function init(){
		parent::init();
		Yii::$app->user->enableSession = false;
		Yii::$app->response->format = Response::FORMAT_JSON;
	}
}
