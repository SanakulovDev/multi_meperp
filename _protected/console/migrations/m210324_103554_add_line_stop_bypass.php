<?php
use yii\db\Migration;

/**
 * Class m210323_103554_add_management_role
 */
class m210324_103554_add_line_stop_bypass extends Migration {

  /**
   * {@inheritdoc}
   */
  public function safeUp() {
    $this->addColumn('line_stop', 'bypass', $this->smallInteger()->unsigned()->null()->after('elapsed_minutes'));
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown() {
    $this->dropColumn('line_stop', 'bypass');
  }

}
