<?php

use yii\db\Migration;

/**
 * Class m191209_114623_alter_name_indexes
 */
class m191209_114623_alter_name_indexes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('container', 'container_no', $this->string(100)->notNull());
        $this->alterColumn('consolidation_type', 'name', $this->string(50)->notNull());
        $this->alterColumn('contract_source', 'name', $this->string(100)->notNull());
        $this->alterColumn('contract_subject', 'name', $this->string(150)->notNull());
        $this->alterColumn('delivery_term', 'name', $this->string(50)->notNull());
        $this->alterColumn('factory', 'name', $this->string(150)->notNull());
        $this->alterColumn('payment_term', 'name', $this->string(50)->notNull());
        $this->alterColumn('user', 'username', $this->string(100)->notNull());
        $this->alterColumn('user', 'email', $this->string(191)->notNull());
        $this->alterColumn('user', 'account_activation_token', $this->string(128)->null());
        $this->alterColumn('user', 'password_hash', $this->string(128)->notNull());
        $this->alterColumn('user', 'password_reset_token', $this->string(128)->null());
        $this->alterColumn('warehouse_report_group', 'title', $this->string(100)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    /*public function safeDown()
    {
        echo "m191209_114623_alter_name_indexes cannot be reverted.\n";

        return false;
    }*/
}
