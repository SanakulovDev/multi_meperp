<?php

namespace app\console\controllers;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
	public function actionClearCache()
	{
		Yii::$app->authManager->invalidateCache();
	}
}
