<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%stock_info_wrapper}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%part}}`
 */
class m240127_151816_add_floc_column_part_id_column_qty_column_line_column_to_stock_info_wrapper_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // $this->addColumn('{{%stock_info_wrapper}}', 'floc', $this->integer());
        $this->addColumn('{{%stock_info_wrapper}}', 'part_id', $this->integer());
        $this->addColumn('{{%stock_info_wrapper}}', 'qty', $this->decimal(20,2));
        $this->addColumn('{{%stock_info_wrapper}}', 'line', $this->integer());

        // creates index for column `part_id`
        $this->createIndex(
            '{{%idx-stock_info_wrapper-part_id}}',
            '{{%stock_info_wrapper}}',
            'part_id'
        );

        // add foreign key for table `{{%part}}`
        $this->addForeignKey(
            '{{%fk-stock_info_wrapper-part_id}}',
            '{{%stock_info_wrapper}}',
            'part_id',
            '{{%part}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%part}}`
        $this->dropForeignKey(
            '{{%fk-stock_info_wrapper-part_id}}',
            '{{%stock_info_wrapper}}'
        );

        // drops index for column `part_id`
        $this->dropIndex(
            '{{%idx-stock_info_wrapper-part_id}}',
            '{{%stock_info_wrapper}}'
        );

        $this->dropColumn('{{%stock_info_wrapper}}', 'floc');
        $this->dropColumn('{{%stock_info_wrapper}}', 'part_id');
        $this->dropColumn('{{%stock_info_wrapper}}', 'qty');
        $this->dropColumn('{{%stock_info_wrapper}}', 'line');
    }
}
