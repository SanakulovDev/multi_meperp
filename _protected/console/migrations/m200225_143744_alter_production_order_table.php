<?php

use yii\db\Migration;

/**
 * Class m200225_143744_alter_production_order_table
 */
class m200225_143744_alter_production_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->execute("UPDATE production_order SET is_label=2 WHERE is_label=0 and quantity>0");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->execute("UPDATE production_order SET is_label=0 WHERE is_label=2");
    }
}
