<?php
   
	/**
		* @link      http://www.yiiframework.com/
		* @copyright Copyright (c) 2008 Yii Software LLC
		* @license   http://www.yiiframework.com/license/
		*/
	namespace app\console\controllers;

	use Yii;
	use yii\console\Controller;
  use app\controllers\ReportController;

	/**
		* This command echoes the first argument that you have entered.
		* This command is provided as an example for you to learn how to create console commands.
		* @author Qiang Xue <qiang.xue@gmail.com>
		* @since  2.0
		*/
	class MailController extends Controller{
		/**
			* This command echoes what you have entered as the message.
			* @param string $message the message to be echoed.
			*/
		public function actionSend(){
      
      $access_token = Yii::$app->params['oynaerp_access_token'];
      $from_email = Yii::$app->params['oynaerp_email'];
      $to_email = Yii::$app->params['oynabi_email'];
      
      $reportfolder = 'reportfolder';
      $filename = $reportfolder.'/'.date("Y-m-d").'.json';
      
      
      if (!file_exists($reportfolder)) {
          mkdir($reportfolder, 0777, true);
      }
      
      // Daily coverage
      $daily_coverage = ReportController::getDailyCoverage();
      file_put_contents($filename, json_encode($daily_coverage));
      
      $message = Yii::$app->mailer->compose();
      $message
        ->setFrom($from_email)
        ->setTo($to_email)
        ->setSubject('Daily coverage')
        ->setTextBody($access_token)
        ->attach($filename);
      if($message->send()){
        echo "\n";
        print_r('Daily report sent successfully');
        echo "\n";
      }else{
        echo "\n";
        print_r('Daily report not sent');
        echo "\n";
      }      
      unlink($filename);
      // *******************
      
      // Weekly coverage
      $weekly_coverage = ReportController::getWeeklyCoverage();
      file_put_contents($filename, json_encode($weekly_coverage));
      
      $message = Yii::$app->mailer->compose();
      $message
        ->setFrom($from_email)
        ->setTo($to_email)
        ->setSubject('Weekly coverage')
        ->setTextBody($access_token)
        ->attach($filename);
      if($message->send()){
        echo "\n";
        print_r('Weekly report sent successfully');
        echo "\n";
      }else{
        echo "\n";
        print_r('Weekly report not sent');
        echo "\n";
      }      
      unlink($filename);
      // **************
    
    
    
    
	}
  }
