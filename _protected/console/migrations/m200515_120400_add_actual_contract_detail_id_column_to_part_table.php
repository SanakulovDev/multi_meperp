<?php

use Illuminate\Support\Facades\Schema;
use yii\db\Migration;
use yii\db\Schema as DbSchema;

/**
 * Class m200420_081509_add_lock_column_to_airshipment
 */
class m200515_120400_add_actual_contract_detail_id_column_to_part_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%part}}', 'actual_contract_detail_id', $this->integer()->null());
        $this->addForeignKey('frk-part-actual_contract_detail_id', 'part', 'actual_contract_detail_id', 'contract_detail', 'id', 'set null', 'set null');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('frk-part-actual_contract_detail_id', 'part');
        $this->dropColumn('{{%part}}', 'actual_contract_detail_id');
    }
}
