<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%country_code}}`.
 */
class m200305_055341_create_country_code_table extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
    $this->createTable('{{%country_code}}', [
      'id' => $this->primaryKey(),
      'alpha_2' => $this->char(2)->unique()->notNull(),
      'alpha_3' => $this->char(3)->null(),
      'numeric_code' => $this->smallInteger()->unsigned(),
      'name_en' => $this->string(100)->notNull(),
      'name_ru' => $this->string(100)->notNull(),
    ], $tableOptions);

    $this->addColumn('{{%customer}}', 'country_code_id', $this->integer(11)->null()->after('country_code'));
    $this->addForeignKey('fk_customer_country_code_id',
      '{{%customer}}', 'country_code_id',
      '{{%country_code}}', 'id',
      'SET NULL', 'SET NULL'
    );
    $this->addColumn('{{%supplier}}', 'country_code_id', $this->integer(11)->null()->after('country_code'));
    $this->addForeignKey('fk_supplier_country_code_id',
      '{{%supplier}}', 'country_code_id',
      '{{%country_code}}', 'id',
      'SET NULL', 'SET NULL'
    );
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->dropForeignKey('fk_supplier_country_code_id', '{{%supplier}}');
    $this->dropColumn('{{%supplier}}', 'country_code_id');

    $this->dropForeignKey('fk_customer_country_code_id', '{{%customer}}');
    $this->dropColumn('{{%customer}}', 'country_code_id');

    $this->dropTable('{{%country_code}}');
  }
}
