<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%production_release_fact_history}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%releaseId}}`
 */
class m230817_201858_create_production_release_fact_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%production_release_fact_history}}', [
            'id' => $this->primaryKey(),
            'userId' => $this->integer(),
            'releaseId' => $this->integer(),
            'quantity' => $this->decimal(20, 2),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->append('ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // creates index for column `releaseId`
        $this->createIndex(
            '{{%idx-production_release_fact_history-releaseId}}',
            '{{%production_release_fact_history}}',
            'releaseId'
        );

        // add foreign key for table `{{%releaseId}}`
        $this->addForeignKey(
            '{{%fk-production_release_fact_history-releaseId}}',
            '{{%production_release_fact_history}}',
            'releaseId',
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
        // drops foreign key for table `{{%releaseId}}`
        $this->dropForeignKey(
            '{{%fk-production_release_fact_history-releaseId}}',
            '{{%production_release_fact_history}}'
        );

        // drops index for column `releaseId`
        $this->dropIndex(
            '{{%idx-production_release_fact_history-releaseId}}',
            '{{%production_release_fact_history}}'
        );

        $this->dropTable('{{%production_release_fact_history}}');
    }
}
