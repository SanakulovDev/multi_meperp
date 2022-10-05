<?php
use yii\db\Migration;

/**
 * Handles the creation of table `{{%line_stop}}`.
 */
class m210303_061819_create_line_stop_table extends Migration {

  /**
   * {@inheritdoc}
   */
  public function safeUp() {
    $tableOptions = 'ENGINE=InnoDB';
    $this->createTable(
      '{{%line_stop}}',
      [
        'id' => $this->primaryKey(11),
        'part_production_monitor_id' => $this->integer()->notNull(),
        'type' => $this->tinyInteger()->unsigned()->notNull(),
        'start_time' => $this->dateTime()->notNull(),
        'end_time' => $this->dateTime(),
        'elapsed_minutes' => $this->integer()->unsigned(),
        'line_stop_reason_id' => $this->integer()->notNull(),
        'remark' => $this->string(),
        'status' => $this->tinyInteger()->unsigned()->notNull(),
        'rejection_remark' => $this->string(),
        'created_by' => $this->integer(11)->null()->defaultValue(null),
        'created_at' => $this->integer(11)->notNull(),
        'updated_by' => $this->integer(11)->null()->defaultValue(null),
        'updated_at' => $this->integer(11)->null()->defaultValue(null),
      ], $tableOptions
    );
    $this->addForeignKey('frk-line_stop-part_production_monitor_id', 'line_stop', 'part_production_monitor_id', 'part_production_monitor', 'id');
    $this->addForeignKey('frk-line_stop-line_stop_reason_id', 'line_stop', 'line_stop_reason_id', 'line_stop_reason', 'id');

  }

  /**
   * {@inheritdoc}
   */
  public function safeDown() {
    $this->dropForeignKey('frk-line_stop-part_production_monitor_id', 'line_stop');
    $this->dropForeignKey('frk-line_stop-line_stop_reason_id', 'line_stop');
    $this->dropTable('{{%line_stop}}');
  }

}
