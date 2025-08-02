<?php

namespace app\console\controllers;

use app\models\Part;
use app\models\PartPartWide;
use Yii;
use yii\console\Controller;

class PriceController extends Controller
{
	public function actionUpdate(){

		$start_price = microtime(true);

		$parts = Part::find()
		->where([
			'status' => Part::STATUS_ACTIVE,
			'state' => Part::STATE_RAW,
		])
		->all();

		foreach ($parts as $part) {
			$actualContractDetail = $part->findActualContract($part->contract_source_id);
			if($actualContractDetail){
				$part->actual_contract_detail_id = $actualContractDetail->id;
				$part->save();
			}
			

		}

		$end_price = microtime(true);
		$dur_price = $end_price - $start_price;
		echo "\nprice: " . round($dur_price, 5) . " sec\n";

	}
}
