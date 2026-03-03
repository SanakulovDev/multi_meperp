<?php
	use yii\db\Migration;
	use yii\helpers\Console;

	class m200330_160000_add_row_bom_sequence_type_table extends Migration{

		public function Up(){
			Yii::$app->db->createCommand()->batchInsert(
				'sequence_type', ['name', 'description'],
				[
					['bomVersion', 'BOM(part_part) last version'],
				]
			)->execute();

			Yii::$app->db->createCommand(
				"INSERT IGNORE sequence ( sequence_type_id, last_seq)  VALUES ( (select id from sequence_type where name='bomVersion'), 0)"
			)->execute();
		}

	}