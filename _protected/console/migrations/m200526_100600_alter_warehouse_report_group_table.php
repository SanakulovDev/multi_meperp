<?php

use Illuminate\Support\Facades\Schema;
use yii\db\Migration;
use yii\db\Schema as DbSchema;

class m200526_100600_alter_warehouse_report_group_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk_warehouse_report_group_created_by', 'warehouse_report_group');
        $this->dropForeignKey('fk_warehouse_report_group_updated_by', 'warehouse_report_group');
        $this->dropColumn('{{%warehouse_report_group}}', 'created_by');
        $this->dropColumn('{{%warehouse_report_group}}', 'created_at');
        $this->dropColumn('{{%warehouse_report_group}}', 'updated_by');
        $this->dropColumn('{{%warehouse_report_group}}', 'updated_at');

    }

   
}
