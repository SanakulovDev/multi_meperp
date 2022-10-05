<?php
	use yii\db\Migration;
	use yii\helpers\Console;

	class m200421_162802_add_fg_invoice_detail_part_data_permission extends Migration{

		public function Up(){
			$query = Yii::$app->db->queryBuilder->batchInsert(
				'auth_item',
				["name", "type"],
				[
					['fg-invoice-detail-part-data', 2],
				]
			);
			$query = str_replace('INSERT INTO', 'INSERT IGNORE', $query);
			Yii::$app->db->createCommand($query)->execute();
			Yii::$app->db->createCommand(
				"INSERT INTO auth_item_child(parent, child) VALUES
               ('admin', 'fg-invoice-detail-part-data'),
               ('superadmin', 'fg-invoice-detail-part-data')
            "
			)->execute();
		}

	}