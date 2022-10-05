<?php

namespace app\modules\api\controllers;

use app\models\Pfep;
use Yii;
use yii\helpers\ArrayHelper;

class PfepController extends BaseController {
	

	public function actionUpdate() {
		
		$transaction = Yii::$app->db->beginTransaction();

		Yii::$app->db->createCommand()->truncateTable('pfep')->execute();
		
		$content = json_decode($_REQUEST['content']);
		
		$success = true;
		foreach ($content as $item) {

			$pfep = new Pfep();

			$itemArray = ArrayHelper::toArray($item);
			$itemArray['created_at'] = date('Y-m-d H:i:s');
			$pfep->load([
				'Pfep' => $itemArray
			]);

			if(!$pfep->save()){
				$success = false;
			}
		}

		if($success){
			$transaction->commit();
			return ['status' => 'success', 'message' => 'Pfep data successfully inserted to DB.'];
		}else{
			$transaction->rollBack();
			return ['status' => 'fail', 'message' => 'Pfep data NOT inserted to DB. Something went wrong.'];
		}
		
	}


}