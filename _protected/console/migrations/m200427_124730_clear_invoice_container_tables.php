<?php

use yii\db\Migration;

/**
 * Class m200427_124730_clear_invoice_container_tables
 */
class m200427_124730_clear_invoice_container_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $query = "DELETE invoice FROM invoice LEFT JOIN container_invoice ON invoice.id=container_invoice.invoice_id LEFT JOIN gtd_invoice ON invoice.id=gtd_invoice.invoice_id WHERE container_invoice.id IS NULL and gtd_invoice.id IS NULL;";
        Yii::$app->db->createCommand($query)->execute();
    }

    public function safeDown()
    {
        return true;
    }
   
}
