<?php

use Illuminate\Support\Facades\Schema;
use yii\db\Migration;
use yii\db\Schema as DbSchema;

class m200526_100100_alter_document_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk_document_type_created_by', 'document_type');
        $this->dropForeignKey('fk_document_type_updated_by', 'document_type');
        $this->dropColumn('{{%document_type}}', 'created_by');
        $this->dropColumn('{{%document_type}}', 'created_at');
        $this->dropColumn('{{%document_type}}', 'updated_by');
        $this->dropColumn('{{%document_type}}', 'updated_at');

    }

   
}
