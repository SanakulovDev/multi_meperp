<?php

use yii\db\Migration;

/**
 * Class m200420_081509_add_lock_column_to_airshipment
 */
class m200420_081509_add_lock_column_to_airshipment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%air_shipment}}', 'status', $this->tinyInteger(1)->defaultValue(1));
        Yii::$app->db->createCommand(
			"INSERT IGNORE `auth_item`(`name`, `type`) 
                      VALUES 
                      ('air-shipment-lock',2);

            INSERT IGNORE `auth_item_child`(`parent`, `child`) 
                      VALUES 
                      ('superadmin','air-shipment-lock');")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%air_shipment}}', 'status');
    }
}
