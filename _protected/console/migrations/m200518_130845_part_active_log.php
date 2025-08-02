<?php
use yii\db\Migration;

class m200518_130845_part_active_log extends Migration {

  public function init() {
    $this->db = 'db';
    parent::init();
  }

  public function safeUp() {
    $tableOptions = 'ENGINE=InnoDB';
    $this->createTable('{{%part_active_log}}', [
      'id' => $this->primaryKey(10)->unsigned(),
      'part_id' => $this->integer(11)->notNull(),
      'begin_date' => $this->date()->notNull()->defaultValue(date('Y-m-d')), //Expression("CURRENT_TIMESTAMP"),
      'end_date' => $this->date()->notNull()->defaultValue('9999-12-31'),
      'status' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0),
    ], $tableOptions);
    $this->addCommentOnTable('{{%part_active_log}}', 'Part status & is_plan log');
    $this->createIndex('part_bdt_edt', '{{%part_active_log}}', ['part_id', 'begin_date', 'end_date'], true);
    $this->addForeignKey(
      'fk_part_active_log_part_id',
      '{{%part_active_log}}', 'part_id',
      '{{%part}}', 'id',
      'CASCADE', 'CASCADE'
    );
    /**  dump data */
    Yii::$app->db->createCommand(
      "INSERT INTO part_active_log (part_id, begin_date, end_date, status) 
            (
               SELECT id, from_unixtime(created_at, '%Y-%m-%d'),'9999-12-31',1 FROM part 
               WHERE status = 1 
            );
            INSERT IGNORE auth_item_child (parent, child) 
            VALUES ('observer', 'coverage-balance-index'),('observer', 'invoice-index');
          "
    )->execute();
  }

  public function safeDown() {
    $this->dropForeignKey('fk_part_active_log_part_id', '{{%part_active_log}}');
    $this->dropTable('{{%part_active_log}}');
  }

}
