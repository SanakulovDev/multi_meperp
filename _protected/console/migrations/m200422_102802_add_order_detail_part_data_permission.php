<?php
	use yii\db\Migration;
	use yii\helpers\Console;

	class m200422_102802_add_order_detail_part_data_permission extends Migration{

		public function Up(){
			$query = Yii::$app->db->queryBuilder->batchInsert(
				'auth_item',
				["name", "type"],
				[
					['part-order-detail-update', 2],
				]
			);
			$query = str_replace('INSERT INTO', 'INSERT IGNORE', $query);
			Yii::$app->db->createCommand($query)->execute();
			Yii::$app->db->createCommand(
				"INSERT IGNORE auth_item_child(parent, child) VALUES
               ('admin', 'part-order-detail-update'),
               ('superadmin', 'part-order-detail-update'),
               ('buyer', 'part-order-detail-update'),
               ('mfu', 'part-order-detail-update');
            "
			)->execute();
		}

	}