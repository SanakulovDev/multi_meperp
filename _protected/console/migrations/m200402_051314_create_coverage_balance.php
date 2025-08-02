<?php

use yii\db\Migration;

/**
 * Class m200402_051314_create_coverage_balance
 */
class m200402_051314_create_coverage_balance extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable('{{%coverage_balance}}', [
				'id' => $this->primaryKey(),
				'supplier_id' => $this->integer(11)->notNull(),
				'period' => $this->date()->notNull(),
				'debt' => $this->decimal(10, 2)->defaultValue(0),
				'paid' => $this->decimal(10, 2)->defaultValue(0)
			], $tableOptions);

			$this->addForeignKey('frk-coverage_balance-supplier_id',
                             '{{%coverage_balance}}', 'supplier_id',
														 '{{%supplier}}', 'id',
														 'CASCADE', 'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
			$this->dropTable('{{%coverage_balance}}');
    }
}
