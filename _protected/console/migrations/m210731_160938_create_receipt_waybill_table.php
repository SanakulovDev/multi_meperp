<?php

use yii\db\Migration;

/**
 * Class m210731_160938_create_receipt_waybill_table
 */
class m210731_160938_create_receipt_waybill_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
      $this->createTable('{{%receipt_waybill}}', [
        'id' => $this->primaryKey(11),
        'waybill_id' => $this->integer(11)->notNull(),
        'recept_control_id' => $this->integer(11)->notNull(),
        'amount' => $this->decimal(25, 10)->notNull(),
        'created_at' => $this->integer(11)->notNull(),
        'created_by' => $this->integer(11)->notNull(),
        'updated_by' => $this->integer(11)->null()->defaultValue(null),
        'updated_at' => $this->integer(11)->null()->defaultValue(null),
      ], $tableOptions);

      $this->addForeignKey(
        'fk_receipt_waybill_waybill_id',
        '{{%receipt_waybill}}', 'waybill_id',
        '{{%waybill}}', 'id',
        'cascade', 'cascade'
      );
      $this->addForeignKey(
        'fk_receipt_waybill_recept_control_id',
        '{{%receipt_waybill}}', 'recept_control_id',
        '{{%recept_control}}', 'id',
        'cascade', 'cascade'
      );

      // create permissions
      $authManager = Yii::$app->getAuthManager();
      Yii::$app->db
        ->createCommand(
          "INSERT IGNORE `auth_item`(`name`, `type`) 
				VALUES('receipt-waybill-index',2),
				('receipt-waybill-create',2),
				('receipt-waybill-update',2),
				('receipt-waybill-delete',2),
				('receipt-waybill-xls',2)"
        )
        ->execute();
      Yii::$app->db
        ->createCommand(
          "INSERT IGNORE `auth_item_child`(`parent`, `child`) 
				VALUES	
				('admin', 'receipt-waybill-index'),
				('admin', 'receipt-waybill-create'),
				('admin', 'receipt-waybill-update'),
				('admin', 'receipt-waybill-delete'),
				('admin', 'receipt-waybill-xls'),			
				('superadmin', 'receipt-waybill-index'),
				('superadmin', 'receipt-waybill-create'),
				('superadmin', 'receipt-waybill-update'),
				('superadmin', 'receipt-waybill-delete'),
				('superadmin', 'receipt-waybill-xls')"
        )
        ->execute();
      $authManager->invalidateCache();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->dropTable('{{%receipt_waybill}}');
    }
}
