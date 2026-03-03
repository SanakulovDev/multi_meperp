<?php
use yii\db\Migration;

/**
 * Handles the creation of table `{{%fg_invoice_receipt}}`.
 */
class m210625_103928_create_fg_invoice_receipt_table extends Migration {

  /**
   * {@inheritdoc}
   */
  public function safeUp() {
    $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
    $this->createTable('{{%fg_invoice_receipt}}', [
      'id' => $this->primaryKey(11),
      'fg_invoice_id' => $this->integer(11)->notNull(),
      'recept_control_id' => $this->integer(11)->notNull(),
      'amount' => $this->decimal(25, 10)->notNull(),
      'created_at' => $this->integer(11)->notNull(),
      'created_by' => $this->integer(11)->notNull(),
      'updated_by' => $this->integer(11)->null()->defaultValue(null),
      'updated_at' => $this->integer(11)->null()->defaultValue(null),
    ], $tableOptions);

    $this->addForeignKey(
      'fk_fg_invoice_receipt_fg_invoice_id',
      '{{%fg_invoice_receipt}}', 'fg_invoice_id',
      '{{%fg_invoice}}', 'id',
      'cascade', 'cascade'
    );
    $this->addForeignKey(
      'fk_fg_invoice_receipt_recept_control_id',
      '{{%fg_invoice_receipt}}', 'recept_control_id',
      '{{%recept_control}}', 'id',
      'cascade', 'cascade'
    );

    // create permissions
    $authManager = Yii::$app->getAuthManager();
    Yii::$app->db
      ->createCommand(
        "INSERT IGNORE `auth_item`(`name`, `type`) 
				VALUES('fg-invoice-receipt-index',2),
				('fg-invoice-receipt-create',2),
				('fg-invoice-receipt-update',2),
				('fg-invoice-receipt-delete',2),
				('fg-invoice-receipt-xls',2)"
      )
      ->execute();
    Yii::$app->db
      ->createCommand(
        "INSERT IGNORE `auth_item_child`(`parent`, `child`) 
				VALUES	
				('admin', 'fg-invoice-receipt-index'),
				('admin', 'fg-invoice-receipt-create'),
				('admin', 'fg-invoice-receipt-update'),
				('admin', 'fg-invoice-receipt-delete'),
				('admin', 'fg-invoice-receipt-xls'),			
				('superadmin', 'fg-invoice-receipt-index'),
				('superadmin', 'fg-invoice-receipt-create'),
				('superadmin', 'fg-invoice-receipt-update'),
				('superadmin', 'fg-invoice-receipt-delete'),
				('superadmin', 'fg-invoice-receipt-xls')"
      )
      ->execute();
    $authManager->invalidateCache();
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown() {
    $this->dropTable('{{%fg_invoice_receipt}}');
  }

}
