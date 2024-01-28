<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%stock_info_sub}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%stock_info}}`
 * - `{{%stock_info_wrapper}}`
 * - `{{%p_order}}`
 * - `{{%give_user}}`
 */
class m240127_185231_create_stock_info_sub_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%stock_info_sub}}', [
            'id' => $this->primaryKey(),
            'stock_info_id' => $this->integer(),
            'stock_info_wrapper_id' => $this->integer(),
            'p_order_id' => $this->integer(),
            'qty' => $this->decimal(20,2),
            'created_at' => $this->timestamp()->null()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultValue(null)->append('ON UPDATE CURRENT_TIMESTAMP'),
            'give_user_id' => $this->integer(),
        ]);

        // creates index for column `stock_info_id`
        $this->createIndex(
            '{{%idx-stock_info_sub-stock_info_id}}',
            '{{%stock_info_sub}}',
            'stock_info_id'
        );

        // add foreign key for table `{{%stock_info}}`
        $this->addForeignKey(
            '{{%fk-stock_info_sub-stock_info_id}}',
            '{{%stock_info_sub}}',
            'stock_info_id',
            '{{%stock_info}}',
            'id',
            'CASCADE'
        );

        // creates index for column `stock_info_wrapper_id`
        $this->createIndex(
            '{{%idx-stock_info_sub-stock_info_wrapper_id}}',
            '{{%stock_info_sub}}',
            'stock_info_wrapper_id'
        );

        // add foreign key for table `{{%stock_info_wrapper}}`
        $this->addForeignKey(
            '{{%fk-stock_info_sub-stock_info_wrapper_id}}',
            '{{%stock_info_sub}}',
            'stock_info_wrapper_id',
            '{{%stock_info_wrapper}}',
            'id',
            'CASCADE'
        );

        // creates index for column `p_order_id`
        $this->createIndex(
            '{{%idx-stock_info_sub-p_order_id}}',
            '{{%stock_info_sub}}',
            'p_order_id'
        );

        // add foreign key for table `{{%p_order}}`
        $this->addForeignKey(
            '{{%fk-stock_info_sub-p_order_id}}',
            '{{%stock_info_sub}}',
            'p_order_id',
            '{{%production_order}}',
            'id',
            'CASCADE'
        );

        // creates index for column `give_user_id`
        $this->createIndex(
            '{{%idx-stock_info_sub-give_user_id}}',
            '{{%stock_info_sub}}',
            'give_user_id'
        );

        // add foreign key for table `{{%give_user}}`
        $this->addForeignKey(
            '{{%fk-stock_info_sub-give_user_id}}',
            '{{%stock_info_sub}}',
            'give_user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%stock_info}}`
        $this->dropForeignKey(
            '{{%fk-stock_info_sub-stock_info_id}}',
            '{{%stock_info_sub}}'
        );

        // drops index for column `stock_info_id`
        $this->dropIndex(
            '{{%idx-stock_info_sub-stock_info_id}}',
            '{{%stock_info_sub}}'
        );

        // drops foreign key for table `{{%stock_info_wrapper}}`
        $this->dropForeignKey(
            '{{%fk-stock_info_sub-stock_info_wrapper_id}}',
            '{{%stock_info_sub}}'
        );

        // drops index for column `stock_info_wrapper_id`
        $this->dropIndex(
            '{{%idx-stock_info_sub-stock_info_wrapper_id}}',
            '{{%stock_info_sub}}'
        );

        // drops foreign key for table `{{%p_order}}`
        $this->dropForeignKey(
            '{{%fk-stock_info_sub-p_order_id}}',
            '{{%stock_info_sub}}'
        );

        // drops index for column `p_order_id`
        $this->dropIndex(
            '{{%idx-stock_info_sub-p_order_id}}',
            '{{%stock_info_sub}}'
        );

        // drops foreign key for table `{{%give_user}}`
        $this->dropForeignKey(
            '{{%fk-stock_info_sub-give_user_id}}',
            '{{%stock_info_sub}}'
        );

        // drops index for column `give_user_id`
        $this->dropIndex(
            '{{%idx-stock_info_sub-give_user_id}}',
            '{{%stock_info_sub}}'
        );

        $this->dropTable('{{%stock_info_sub}}');
    }
}
