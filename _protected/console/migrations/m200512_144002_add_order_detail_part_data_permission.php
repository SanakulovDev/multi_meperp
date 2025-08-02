<?php
	use yii\db\Migration;
	use yii\helpers\Console;

	class m200512_144002_add_order_detail_part_data_permission extends Migration{

		public function Up(){
			$query = Yii::$app->db->queryBuilder->batchInsert(
				'auth_item',
				["name", "type"],
				[
					['part-order-detail-delete', 2],
				]
			);
			$query = str_replace('INSERT INTO', 'INSERT IGNORE', $query);
			Yii::$app->db->createCommand($query)->execute();
			Yii::$app->db->createCommand(
				"INSERT IGNORE auth_item_child(parent, child) VALUES
               ('admin', 'part-order-detail-delete'),
               ('superadmin', 'part-order-detail-delete'),
               ('buyer', 'part-order-detail-delete'),
               ('mfu', 'part-order-detail-delete');
            "
			)->execute();
		}

	}