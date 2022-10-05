<?php

use yii\db\Migration;

/**
 * Class m200407_041819_add_fg_invoice_part_list
 */
class m200407_041819_add_fg_invoice_part_list extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		Yii::$app->db->createCommand(
			"INSERT IGNORE `auth_item`(`name`, `type`) 
                      VALUES 
                      ('fg-invoice-part-list', 2),
                      ('fg-invoice-part-data', 2)"
		)->execute();

		Yii::$app->db->createCommand(
			"INSERT IGNORE `auth_item_child`(`parent`, `child`) 
                      VALUES 
                      ('sales','fg-invoice-part-list'),
                      ('shipper','fg-invoice-part-list'),
                      ('admin','fg-invoice-part-list'),
                      ('superadmin','fg-invoice-part-list'),
                      ('sales','fg-invoice-part-data'),
                      ('shipper','fg-invoice-part-data'),
                      ('admin','fg-invoice-part-data'),
                      ('superadmin','fg-invoice-part-data')"
		)->execute();
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		return true;
	}
}