<?php
use yii\db\Migration;

/**
 * Handles the creation of table `{{%recept_control}}`.
 */
class m210620_082826_create_recept_control_table extends Migration {

  /**
   * {@inheritdoc}
   */
  public function safeUp() {
    $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
    $this->createTable('{{%recept_control}}', [
      'id' => $this->primaryKey(11),
      'no' => $this->string(100)->notNull(),
      'date' => $this->date()->notNull(),
      'customer_id' => $this->integer(11)->notNull(),
      'payment_term' => $this->integer(11)->notNull(),
      'amount' => $this->decimal(25, 10)->notNull(),
      'sales_contract_id' => $this->integer(11)->notNull(),
      'created_at' => $this->integer(11)->notNull(),
      'created_by' => $this->integer(11)->notNull(),
      'updated_by' => $this->integer(11)->null()->defaultValue(null),
      'updated_at' => $this->integer(11)->null()->defaultValue(null),
    ], $tableOptions);
    $this->createIndex('fk_recept_control_sales_contract_id', '{{%recept_control}}', ['sales_contract_id'], false);
    $this->createIndex('fk_recept_control_customer_id', '{{%recept_control}}', ['customer_id'], false);
    // create permissions
    $authManager = Yii::$app->getAuthManager();
    Yii::$app->db
      ->createCommand(
        "INSERT IGNORE `auth_item`(`name`, `type`) 
				VALUES('recept-control-index',2),
				('recept-control-create',2),
				('recept-control-update',2),
				('recept-control-delete',2),
				('recept-control-xls',2)"
      )
      ->execute();
    Yii::$app->db
      ->createCommand(
        "INSERT IGNORE `auth_item_child`(`parent`, `child`) 
				VALUES	
				('admin', 'recept-control-index'),
				('admin', 'recept-control-create'),
				('admin', 'recept-control-update'),
				('admin', 'recept-control-delete'),
				('admin', 'recept-control-xls'),			
				('superadmin', 'recept-control-index'),
				('superadmin', 'recept-control-create'),
				('superadmin', 'recept-control-update'),
				('superadmin', 'recept-control-delete'),
				('superadmin', 'recept-control-xls')"
      )
      ->execute();
    $authManager->invalidateCache();
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown() {
    $this->dropTable('{{%recept_control}}');
  }

}
