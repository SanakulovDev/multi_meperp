<?php

use yii\db\Migration;

/**
 * Makes sales_contract_id nullable in fg_invoice_payment.
 */
class m260506_120000_make_sales_contract_id_nullable_in_fg_invoice_payment extends Migration
{
    public function safeUp()
    {
        $schema = $this->db->schema->getTableSchema('fg_invoice_payment', true);
        if ($schema === null || !isset($schema->columns['sales_contract_id'])) {
            return;
        }

        $this->alterColumn(
            'fg_invoice_payment',
            'sales_contract_id',
            $this->integer()->null()->defaultValue(null)
        );
    }

    public function safeDown()
    {
        $schema = $this->db->schema->getTableSchema('fg_invoice_payment', true);
        if ($schema === null || !isset($schema->columns['sales_contract_id'])) {
            return;
        }

        $nullCount = (int) $this->db->createCommand(
            'SELECT COUNT(*) FROM fg_invoice_payment WHERE sales_contract_id IS NULL'
        )->queryScalar();

        if ($nullCount > 0) {
            echo "Cannot revert m260506_120000_make_sales_contract_id_nullable_in_fg_invoice_payment: fg_invoice_payment.sales_contract_id contains NULL values.\n";
            return false;
        }

        $this->alterColumn(
            'fg_invoice_payment',
            'sales_contract_id',
            $this->integer()->notNull()
        );
    }
}
