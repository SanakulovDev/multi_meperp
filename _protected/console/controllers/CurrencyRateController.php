<?php
	/**
		* @link      http://www.yiiframework.com/
		* @copyright Copyright (c) 2008 Yii Software LLC
		* @license   http://www.yiiframework.com/license/
		*/
	namespace app\console\controllers;

	use yii\console\Controller;
  use app\models\CurrencyRate;
  use app\models\Currency;

	/**
		* This command echoes the first argument that you have entered.
		* This command is provided as an example for you to learn how to create console commands.
		* @author Qiang Xue <qiang.xue@gmail.com>
		* @since  2.0
		*/
	class CurrencyRateController extends Controller{
		/**
			* This command echoes what you have entered as the message.
			* @param string $message the message to be echoed.
			*/
		public function actionUpdate(){
			$currency_rates = json_decode(file_get_contents('http://cbu.uz/ru/arkhiv-kursov-valyut/json'));
      foreach($currency_rates as $crate){
        if(in_array(trim($crate->Ccy),['USD','EUR','RUB'])){
          $currency_id = Currency::findOneCurrencyCode(trim($crate->Ccy))->id;
          //$rate_date = date('Y-m-d', strtotime(trim($crate->Date)));
          $rate_date = date('Y-m-d');
          $modelCurrRate = CurrencyRate::find()->where(['currency_id' => $currency_id,'rate_date' => $rate_date])->one();
          if($modelCurrRate){
            //update
            $modelCurrRate->uzs_value = trim($crate->Rate);
            $modelCurrRate->created_by = null;
            $modelCurrRate->updated_by = null;
            $modelCurrRate->updated_at = time();
            if(!$modelCurrRate->save()){
              echo "\n Update \n";
              print_r($modelCurrRate->errors);
              echo "\n";
              die;
            }
          }else{
            //insert
            $modelCurrRate = new CurrencyRate();
            $modelCurrRate->currency_id = $currency_id;
            $modelCurrRate->rate_date = $rate_date;
            $modelCurrRate->uzs_value = trim($crate->Rate);
            $modelCurrRate->created_at = time();
            $modelCurrRate->updated_at = time();
            if(!$modelCurrRate->save()){
              echo "\n Insert \n";
              print_r($modelCurrRate->errors);
              echo "\n";
              die;
            }
            
          }
        }
        
      }
		}
	}
