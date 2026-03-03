<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%release_item}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%release}}`
 */
class m230804_133829_create_release_item_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%production_release_item}}', [
            'id' => $this->primaryKey(),
            'release_id' => $this->integer(),
            'qty' => $this->decimal(20,2),
            'comment' => $this->string(),
            'status' => $this->integer(),
            'created' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->append('ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // creates index for column `release_id`
        $this->createIndex(
            '{{%idx-release_item-release_id}}',
            '{{%production_release_item}}',
            'release_id'
        );

        // add foreign key for table `{{%release}}`
        $this->addForeignKey(
            '{{%fk-release_item-release_id}}',
            '{{%production_release_item}}',
            'release_id',
            '{{%production_release}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%release}}`
        $this->dropForeignKey(
            '{{%fk-release_item-release_id}}',
            '{{%production_release_item}}'
        );

        // drops index for column `release_id`
        $this->dropIndex(
            '{{%idx-release_item-release_id}}',
            '{{%production_release_item}}'
        );

        $this->dropTable('{{%production_release_item}}');
    }
}
