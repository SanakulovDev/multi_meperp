<?php
	use yii\db\Migration;
	use yii\helpers\Console;

	class m200324_114502_add_part_packing_print_permission extends Migration{

		public function Up(){
			$query = Yii::$app->db->queryBuilder->batchInsert(
				'auth_item',
				["name", "type"],
				[
					['part-packing-print', 2],
				]
			);
			$query = str_replace('INSERT INTO', 'INSERT IGNORE', $query);
			Yii::$app->db->createCommand($query)->execute();
			Yii::$app->db->createCommand(
				"INSERT INTO auth_item_child(parent, child) VALUES('superadmin', 'part-packing-print')"
			)->execute();
		}

	}