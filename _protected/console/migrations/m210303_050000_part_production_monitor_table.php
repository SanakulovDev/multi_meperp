<?php
use yii\db\Migration;

class m210303_050000_part_production_monitor_table extends Migration {

  public function init() {
    $this->db = 'db';
    parent::init();
  }

  public function safeUp() {
    $tableOptions = 'ENGINE=InnoDB';
    $this->createTable(
      'part_production_monitor',
      [
        'id' => $this->primaryKey(11),
        'production_monitor_id' => $this->integer(11)->notNull(),
        'part_id' => $this->integer(11)->notNull(),
        'produced_qty' => $this->decimal(25, 10)->notNull()->comment('production label create da update qilinadi'),
        'repaired_qty' => $this->decimal(25, 10)->null(),
        'broken_qty' => $this->decimal(25, 10)->null(),
        'actual_production_time' => $this->smallInteger(6)->null(),
        'created_by' => $this->integer(11),
        'created_at' => $this->integer(11)->notNull(),
        'updated_by' => $this->integer(11)->null()->defaultValue(null),
        'updated_at' => $this->integer(11)->null()->defaultValue(null),
      ], $tableOptions
    );
    $this->addForeignKey('fk-part_production_monitor-production_monitor_id', 'part_production_monitor', 'production_monitor_id', 'production_monitor', 'id');
    $this->addForeignKey('fk-part_production_monitor-part_id', 'part_production_monitor', 'part_id', 'part', 'id');
    $this->addForeignKey('fk-part_production_monitor-created_by', 'part_production_monitor', 'created_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
    $this->addForeignKey('fk-part_production_monitor-updated_by', 'part_production_monitor', 'updated_by', '{{%user}}', 'id', 'RESTRICT', 'RESTRICT');
  }

  public function safeDown() {
    $this->dropForeignKey('fk-part_production_monitor-production_monitor_id', 'part_production_monitor');
    $this->dropForeignKey('fk-part_production_monitor-part_id', 'part_production_monitor');
    $this->dropForeignKey('fk-part_production_monitor-created_by', 'part_production_monitor');
    $this->dropForeignKey('fk-part_production_monitor-updated_by', 'part_production_monitor');
    $this->dropTable('part_production_monitor');
  }

}
