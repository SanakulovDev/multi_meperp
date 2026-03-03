<?php

use yii\db\Migration;

/**
 * Class m230613_185713_ostatka_gp_peremestit_ostatka
 */
class m230613_185713_ostatka_gp_peremestit_ostatka extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //report table dan stock-dashboard action ni report_group id ni 2 ga o'zgartirish
        $this->update('report', ['report_group_id' => 2], ['action' => 'stock-dashboard']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->update('report', ['report_group_id' => 4], ['action' => 'stock-dashboard']);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230613_185713_ostatka_gp_peremestit_ostatka cannot be reverted.\n";

        return false;
    }
    */
}
