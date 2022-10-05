<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sales_plan}}`.
 */
class m210607_062248_create_sales_plan_table extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
    $this->createTable(
      '{{%sales_plan}}',
      [
        'id' => $this->primaryKey(),
        'part_id' => $this->integer(10)
          ->notNull()
          ->comment('Part; semi;FG'),
        'customer_id' => $this->integer(11)
          ->notNull()
          ->comment('Customer'),
        'target_date' => $this->date()->notNull(),
        'target_qty' => $this->smallInteger(5)
          ->unsigned()
          ->notNull()
          ->defaultValue(0),
      ],
      $tableOptions
    );

    $this->createIndex('part_customer_dt', '{{%sales_plan}}', ['part_id', 'target_date', 'customer_id'], true);
    $this->createIndex('fk_sales_plan_part_id', '{{%sales_plan}}', ['part_id'], false);
    $this->createIndex('fk_sales_plan_customer_id', '{{%sales_plan}}', ['customer_id'], false);

    // create permissions

    $authManager = Yii::$app->getAuthManager();
    Yii::$app->db
      ->createCommand(
        "INSERT IGNORE `auth_item`(`name`, `type`) 
				VALUES('sales-plan-index',2),
				('sales-plan-create',2),
				('sales-plan-update',2),
				('sales-plan-delete',2),
				('sales-plan-xls',2)"
      )
      ->execute();
    Yii::$app->db
      ->createCommand(
        "INSERT IGNORE `auth_item_child`(`parent`, `child`) 
				VALUES	
				('admin', 'sales-plan-index'),
				('admin', 'sales-plan-create'),
				('admin', 'sales-plan-update'),
				('admin', 'sales-plan-delete'),
				('admin', 'sales-plan-xls'),
				
				('superadmin', 'sales-plan-index'),
				('superadmin', 'sales-plan-create'),
				('superadmin', 'sales-plan-update'),
				('superadmin', 'sales-plan-delete'),
				('superadmin', 'sales-plan-xls')"
      )
      ->execute();
    $authManager->invalidateCache();
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->dropIndex('fk_sales_plan_customer_id', '{{%sales_plan}}');
    $this->dropIndex('fk_sales_plan_part_id', '{{%sales_plan}}');
    $this->dropIndex('part_customer_dt', '{{%sales_plan}}');
    $this->dropTable('{{%sales_plan}}');
  }
}
