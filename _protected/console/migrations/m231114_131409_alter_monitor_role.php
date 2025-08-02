<?php

use yii\db\Migration;

/**
 * Class m231114_131409_alter_monitor_role
 */
class m231114_131409_alter_monitor_role extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->batchInsert('{{%auth_item}}', ['name', 'type', 'description', 'rule_name', 'data', 'created_at', 'updated_at'], [
        ['dashboard/analiz', 2, 'dashboard/analiz', null, null, time(), time()],
       ]);
       
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->delete('{{%auth_item}}', ['name' => 'dashboard/analiz']);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231114_131409_alter_monitor_role cannot be reverted.\n";

        return false;
    }
    */
}
