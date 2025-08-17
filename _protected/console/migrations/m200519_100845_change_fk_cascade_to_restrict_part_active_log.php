<?php
use yii\db\Migration;

class m200519_100845_change_fk_cascade_to_restrict_part_active_log extends Migration {

  public function up() {
    $this->dropForeignKey('fk_part_active_log_part_id', '{{%part_active_log}}');
    $this->addForeignKey(
      'fk_part_active_log_part_id',
      '{{%part_active_log}}', 'part_id',
      '{{%part}}', 'id',
      'RESTRICT', 'RESTRICT'
    );
  }

}
