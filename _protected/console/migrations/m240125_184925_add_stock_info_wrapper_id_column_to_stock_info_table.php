<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%stock_info}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%stock_info_wrapper}}`
 */
class m240125_184925_add_stock_info_wrapper_id_column_to_stock_info_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%stock_info}}', 'stock_info_wrapper_id', $this->integer()->after('type_id'));

        // creates index for column `stock_info_wrapper_id`
        $this->createIndex(
            '{{%idx-stock_info-stock_info_wrapper_id}}',
            '{{%stock_info}}',
            'stock_info_wrapper_id'
        );

        // add foreign key for table `{{%stock_info_wrapper}}`
        $this->addForeignKey(
            '{{%fk-stock_info-stock_info_wrapper_id}}',
            '{{%stock_info}}',
            'stock_info_wrapper_id',
            '{{%stock_info_wrapper}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%stock_info_wrapper}}`
        $this->dropForeignKey(
            '{{%fk-stock_info-stock_info_wrapper_id}}',
            '{{%stock_info}}'
        );

        // drops index for column `stock_info_wrapper_id`
        $this->dropIndex(
            '{{%idx-stock_info-stock_info_wrapper_id}}',
            '{{%stock_info}}'
        );

        $this->dropColumn('{{%stock_info}}', 'stock_info_wrapper_id');
    }
}
