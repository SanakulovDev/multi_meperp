<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%pechat_product}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%color}}`
 */
class m230511_184250_add_color_id_column_line_column_comment_column_to_pechat_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%pechat_product}}', 'color_id', $this->integer());
        $this->addColumn('{{%pechat_product}}', 'line', $this->string());
        $this->addColumn('{{%pechat_product}}', 'comment', $this->string());

        // creates index for column `color_id`
        $this->createIndex(
            '{{%idx-pechat_product-color_id}}',
            '{{%pechat_product}}',
            'color_id'
        );

        // add foreign key for table `{{%color}}`
        $this->addForeignKey(
            '{{%fk-pechat_product-color_id}}',
            '{{%pechat_product}}',
            'color_id',
            '{{%part_color}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%color}}`
        $this->dropForeignKey(
            '{{%fk-pechat_product-color_id}}',
            '{{%pechat_product}}'
        );

        // drops index for column `color_id`
        $this->dropIndex(
            '{{%idx-pechat_product-color_id}}',
            '{{%pechat_product}}'
        );

        $this->dropColumn('{{%pechat_product}}', 'color_id');
        $this->dropColumn('{{%pechat_product}}', 'line');
        $this->dropColumn('{{%pechat_product}}', 'comment');
    }
}
