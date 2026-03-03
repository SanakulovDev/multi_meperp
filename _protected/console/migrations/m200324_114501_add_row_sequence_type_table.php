<?php
	use yii\db\Migration;
	use yii\helpers\Console;

	class m200324_114501_add_row_sequence_type_table extends Migration{

		public function Up(){
			Yii::$app->db->createCommand()->batchInsert(
				'sequence_type', ['name', 'description'],
				[
					['supply', 'label for supply'],
				]
			)->execute();
		}

	}