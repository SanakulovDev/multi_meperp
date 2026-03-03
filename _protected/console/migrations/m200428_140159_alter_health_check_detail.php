<?php
use yii\db\Migration;

/**
 * Class m200428_140159_alter_health_check_detail
 */
class m200428_140159_alter_health_check_detail extends Migration {

  /**
   * {@inheritdoc}
   */
  public function safeUp() {
    $this->addColumn(
      '{{%health_check_detail}}',
      'updated_at',
      $this->timestamp()->notNull()
           ->defaultExpression("CURRENT_TIMESTAMP")
           ->append('ON UPDATE CURRENT_TIMESTAMP')
    );
  }

  public function safeDown() {
    $this->dropColumn('{{%health_check_detail}}', 'updated_at');
  }

}
