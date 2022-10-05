<?php
	use yii\db\Migration;
	use yii\helpers\Console;

	class m200417_184502_add_rec_report_table extends Migration{

		public function Up(){
		  $query ="INSERT IGNORE report(`action`, title, description, created_by, created_at, list_order, style) 
            VALUES('coverage-by-vehicle-set', 'Coverage by vehicle set', 'Coverage by vehicle set', 1, 0, 3, 'ion-map:yellow')";
			Yii::$app->db->createCommand( $query )->execute();
		}

	}