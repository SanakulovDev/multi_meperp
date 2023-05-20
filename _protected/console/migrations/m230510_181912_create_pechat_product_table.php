<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%pechat_product}}`.
 */
class m230510_181912_create_pechat_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%pechat_product}}', [
            'id' => $this->primaryKey(),
            'part_id' => $this->integer(),
            'number_lot' => $this->string(),
            'date' => $this->date(),
            'weight_netto' => $this->integer(),
            'weight_brutto' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%pechat_product}}');
    }
}
