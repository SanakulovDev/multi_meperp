<?php
use yii\db\Migration;

class m200427_111920_create_health_check_table extends Migration {

  public function init() {
    $this->db = 'db';
    parent::init();
  }

  public function safeUp() {
    $tableOptions = 'ENGINE=InnoDB';
    $this->createTable('{{%health_check}}', [
      'id' => $this->primaryKey(10)->unsigned(),
      'sort_order' => $this->smallInteger(6)->null()->defaultValue(null),
      'title' => $this->string(100)->notNull(),
      'description' => $this->text()->notNull(),
      'status'=> $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(1)->comment('1-active; 0-inactive;'),
    ], $tableOptions);
    $this->addCommentOnTable('{{%health_check}}', 'Savolnomalarro`yhati');
    $this->createIndex('uk_health_check_title', '{{%health_check}}', ['title'], true);
    $this->createTable('{{%health_check_detail}}', [
      'id' => $this->primaryKey(10)->unsigned(),
      'health_check_id' => $this->integer(10)->unsigned()->notNull(),
      'check_date' => $this->date()->notNull(),
      'status' => $this->string(1)->notNull()->comment('R-red; Y-yellow; G-green;'),
      'description' => $this->string(250)->null()->defaultValue(null)->comment('Status xolatini yozib boriladi'),
    ], $tableOptions);
    $this->addCommentOnTable('{{%health_check_detail}}', 'Savolnomalarning kunlik xolatlari');
    $this->createIndex('uk_health_check_detail_health_check_id_date', '{{%health_check_detail}}', ['health_check_id', 'check_date'], true);
    $this->addForeignKey(
      'fk_health_check_detail_health_check_id',
      '{{%health_check_detail}}', 'health_check_id',
      '{{%health_check}}', 'id',
      'RESTRICT', 'RESTRICT'
    );
  }

  public function safeDown() {
    $this->dropForeignKey('fk_health_check_detail_health_check_id', '{{%health_check_detail}}');
    $this->dropTable('{{%health_check}}');
    $this->dropTable('{{%health_check_detail}}');
  }

}
