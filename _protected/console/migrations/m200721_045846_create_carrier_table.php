<?php
use yii\db\Migration;

/**
 * Handles the creation of table `{{%carrier}}`.
 */
class m200721_045846_create_carrier_table extends Migration {

  /**
   * {@inheritdoc}
   */
  public function safeUp() {
    $this->createTable('{{%carrier}}', [
      'id' => $this->primaryKey(),
      'company_name' => $this->string(100)->notNull(),
      'duns' => $this->string(30)->notNull(),
      'address' => $this->string(255),
      'country_code_id' => $this->integer(11),
      'city' => $this->string(100),
      'postal' => $this->string(30),
      'contact_name' => $this->string(50),
      'contact_position' => $this->string(50),
      'contact_email' => $this->string(50),
      'contact_phone' => $this->string(50),
      'contact_cellular' => $this->string(50),
    ]);
    $this->addForeignKey('fk_carrier_country_code_id',
                         '{{%carrier}}', 'country_code_id',
                         '{{%country_code}}', 'id',
                         'SET NULL', 'SET NULL'
    );

    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item`(`name`, `type`, `description`, `created_at`, `updated_at`) 
              VALUES ('carrier-index', 2, 'List carriers', 1580562767, 1580562767),
                    ('carrier-create', 2, 'Create new carrier info', 1580562767, 1580562767),
                    ('carrier-update', 2, 'Edit carrier info', 1580562767, 1580562767),
                    ('carrier-delete', 2, 'Delete carrier info', 1580562767, 1580562767),
                    ('carrier-xls', 2, 'Download carrier list', 1580562767, 1580562767);
             INSERT IGNORE `auth_item_child`(`parent`, `child`) 
                VALUES 
                ('superadmin','carrier-index'),
                ('superadmin','carrier-create'),
                ('superadmin','carrier-update'),
                ('superadmin','carrier-delete'),
                ('superadmin','carrier-xls'),
                ('admin','carrier-index'),
                ('admin','carrier-create'),
                ('admin','carrier-update'),
                ('admin','carrier-delete'),
                ('admin','carrier-xls'),
                ('observer','carrier-index'),
                ('logistics','carrier-index'),
                ('mrp-logx','carrier-index');"
    )->execute();

  }

  /**
   * {@inheritdoc}
   */
  public function safeDown() {
    $this->dropForeignKey('fk_carrier_country_code_id', '{{%carrier}}');
    $this->dropTable('{{%carrier}}');
  }

}
