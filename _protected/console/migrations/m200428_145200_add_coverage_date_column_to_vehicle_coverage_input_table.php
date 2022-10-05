<?php

use yii\db\Migration;

/**
 * Class m200420_081509_add_lock_column_to_airshipment
 */
class m200428_145200_add_coverage_date_column_to_vehicle_coverage_input_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%vehicle_coverage_input}}', 'coverage_date', $this->date()->null()->after('description'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%vehicle_coverage_input}}', 'coverage_date');
    }
}
