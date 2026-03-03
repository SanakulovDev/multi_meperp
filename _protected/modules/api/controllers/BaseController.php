<?php

namespace app\modules\api\controllers;

use app\modules\api\components\ApiAuth;
use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

class BaseController extends Controller {
	public $serializer = [
		'class' => 'yii\rest\Serializer',
		'collectionEnvelope' => 'data',
	];

	/**
	 * Returns a list of behaviors that this component should behave as.
	 * Here we use RBAC in combination with AccessControl filter.
	 *
	 * @return array
	 */
	public function behaviors() {
		return [
			'corsFilter' => [
				'class' => '\yii\filters\Cors',
				'cors' => [
					'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
					'Access-Control-Request-Headers' => ['*'],
					'Access-Control-Allow-Credentials' => true,
				],
			],
			'contentNegotiator' => [
				'class' => ContentNegotiator::className(),
				'formats' => [
					'application/json' => Response::FORMAT_JSON,
				],
			],
			'authenticator' => [
				'class' => HttpBearerAuth::className()
			],
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'delete' => ['post'],
					'confirm' => ['post'],
				],
			],
		]; // return
	}

	// behaviors

	public function beforeAction($action) {
		$this->enableCsrfValidation = false;
		return parent::beforeAction($action);
	}
}