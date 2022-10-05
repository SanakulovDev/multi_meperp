<?php
use yii\db\Migration;

class m200601_182345_change_part_active_log extends Migration {

  public function up() {
    $this->dropForeignKey('fk_part_active_log_part_id', '{{%part_active_log}}');
    $this->addColumn('{{%part_active_log}}',
      'part_no', $this->string(50)->after('part_id')
    );
    /** update data */
    Yii::$app->db->createCommand(
      "UPDATE part_active_log pal SET part_no = ( SELECT part_no FROM part p WHERE pal.part_id=p.id)"
    )->execute();
    $this->dropColumn('part_active_log', 'part_id');
  }
}
