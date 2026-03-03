<?php
	use yii\db\Migration;
	use yii\helpers\Console;

	class m200401_124502_add_bom_version_permission extends Migration{

		public function Up(){
			$query = Yii::$app->db->queryBuilder->batchInsert(
				'auth_item',
				["name", "type"],
				[
					['part-part-version-index', 2],
				]
			);
			$query = str_replace('INSERT INTO', 'INSERT IGNORE', $query);
			Yii::$app->db->createCommand($query)->execute();
			Yii::$app->db->createCommand(
				"INSERT INTO auth_item_child(parent, child) VALUES('superadmin', 'part-part-version-index')"
			)->execute();
		}

	}