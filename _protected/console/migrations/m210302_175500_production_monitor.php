<?php
use yii\db\Migration;

class m210302_175500_production_monitor extends Migration {

  public function init() {
    $this->db = 'db';
    parent::init();
  }

  public function safeUp() {

    $tableOptions = 'ENGINE=InnoDB';

    $this->createTable(
      '{{%production_monitor}}',
      [
        'id' => $this->primaryKey(11),
        'warehouse_id' => $this->integer()->notNull(),
        'production_date' => $this->date()->notNull(),
        'shift' => $this->tinyInteger()->unsigned()->notNull(),
        'status' => $this->tinyInteger()->unsigned()->notNull(),
        'quality_confirmed_at' => $this->dateTime()->null(),
        'quality_confirmed_by' => $this->integer()->null(),
        'production_completed_at' => $this->dateTime()->null(),
        'production_completed_by' => $this->integer()->null()
      ], $tableOptions
    );
    $this->createIndex('uk-production_monitor-warehouse_id-production_date-shift', 'production_monitor', ['warehouse_id', 'production_date', 'shift'], true);
    $this->addForeignKey('frk-production_monitor-warehouse_id', 'production_monitor', 'warehouse_id', 'warehouse', 'id');
    $this->addForeignKey('frk-production_monitor-quality_confirmed_by', 'production_monitor', 'quality_confirmed_by', 'user', 'id');
    $this->addForeignKey('frk-production_monitor-production_completed_by', 'production_monitor', 'production_completed_by', 'user', 'id');

  }

  public function safeDown() {

    $this->dropForeignKey('frk-production_monitor-warehouse_id', 'production_monitor');
    $this->dropForeignKey('frk-production_monitor-quality_confirmed_by', 'production_monitor');
    $this->dropForeignKey('frk-production_monitor-production_completed_by', 'production_monitor');

    $this->dropTable('{{%production_monitor}}');

  }

}
