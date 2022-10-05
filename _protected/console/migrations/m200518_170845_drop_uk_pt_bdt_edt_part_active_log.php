<?php
use yii\db\Migration;

class m200518_170845_drop_uk_pt_bdt_edt_part_active_log extends Migration {

  public function up() {
    $this->dropForeignKey('fk_part_active_log_part_id', '{{%part_active_log}}');
    $this->dropIndex('part_bdt_edt', '{{%part_active_log}}');
    $this->addForeignKey(
      'fk_part_active_log_part_id',
      '{{%part_active_log}}', 'part_id',
      '{{%part}}', 'id',
      'CASCADE', 'CASCADE'
    );

    $this->addColumn('{{%part_active_log}}', 'updated_by', $this->integer(11));
    $this->addColumn('{{%part_active_log}}', 'updated_at', $this->integer(11));

  }

  public function down() {
    $this->createIndex('part_bdt_edt', '{{%part_active_log}}', ['part_id', 'begin_date', 'end_date'], true);
  }

}
