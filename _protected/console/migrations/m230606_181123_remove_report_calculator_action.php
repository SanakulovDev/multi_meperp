<?php

use yii\db\Migration;

/**
 * Class m230606_181123_remove_report_calculator_action
 */
class m230606_181123_remove_report_calculator_action extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->delete('user_report', ['report_id' => (new \yii\db\Query())->select('id')->from('report')->where(['action' => 'calculate'])->scalar()]);
        // delete from `report` where `action`='calculate-product-index'
        $this->delete('report', ['action' => 'calculate']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230606_181123_remove_report_calculator_action cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230606_181123_remove_report_calculator_action cannot be reverted.\n";

        return false;
    }
    */
}
