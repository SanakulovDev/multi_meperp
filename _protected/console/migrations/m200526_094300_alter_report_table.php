<?php

use Illuminate\Support\Facades\Schema;
use yii\db\Migration;
use yii\db\Schema as DbSchema;

class m200526_094300_alter_report_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%report}}', 'report_group_id', $this->integer()->null());
        $this->addForeignKey('frk-report-report_group_id', 'report', 'report_group_id', 'report_group', 'id', 'set null', 'set null');

        $this->dropForeignKey('fk_report_created_by', 'report');
        $this->dropForeignKey('fk_report_updated_by', 'report');
        $this->dropColumn('{{%report}}', 'created_by');
        $this->dropColumn('{{%report}}', 'created_at');
        $this->dropColumn('{{%report}}', 'updated_by');
        $this->dropColumn('{{%report}}', 'updated_at');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('frk-report-report_group_id', 'report');
        $this->dropColumn('{{%report}}', 'report_group_id');
    }
}
