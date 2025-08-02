<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%stock_info}}`.
 */
class m240123_141636_add_type_id_column_to_stock_info_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%stock_info}}', 'type_id', $this->integer()->after('give_user_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%stock_info}}', 'type_id');
    }
}
