<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%stock_info_wrapper}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%document}}`
 */
class m240125_184558_create_stock_info_wrapper_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%stock_info_wrapper}}', [
            'id' => $this->primaryKey(),
            'code' => $this->string(),
            'warehouse_id' => $this->integer(),
            'type_id' => $this->integer(),
            'give_user_id' => $this->integer(),
            'comment' => $this->string(),
            'document_id' => $this->integer(),
            'created_at' => $this->date(),
            'updated_at' => $this->date(),
        ]);

        // creates index for column `document_id`
        $this->createIndex(
            '{{%idx-stock_info_wrapper-document_id}}',
            '{{%stock_info_wrapper}}',
            'document_id'
        );

        // add foreign key for table `{{%document}}`
        $this->addForeignKey(
            '{{%fk-stock_info_wrapper-document_id}}',
            '{{%stock_info_wrapper}}',
            'document_id',
            '{{%document}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%document}}`
        $this->dropForeignKey(
            '{{%fk-stock_info_wrapper-document_id}}',
            '{{%stock_info_wrapper}}'
        );

        // drops index for column `document_id`
        $this->dropIndex(
            '{{%idx-stock_info_wrapper-document_id}}',
            '{{%stock_info_wrapper}}'
        );

        $this->dropTable('{{%stock_info_wrapper}}');
    }
}
