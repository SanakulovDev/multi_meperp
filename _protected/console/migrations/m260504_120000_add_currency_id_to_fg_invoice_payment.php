<?php

use yii\db\Migration;

/**
 * Adds currency_id to fg_invoice_payment and links it to currency table.
 */
class m260504_120000_add_currency_id_to_fg_invoice_payment extends Migration
{
    public function safeUp()
    {
        $schema = $this->db->schema->getTableSchema('fg_invoice_payment', true);
        if ($schema === null) {
            return;
        }

        $columns = array_keys($schema->columns);
        if (!in_array('currency_id', $columns, true)) {
            $this->addColumn(
                'fg_invoice_payment',
                'currency_id',
                $this->integer()->null()->defaultValue(null)->after('sales_contract_id')
            );
        }

        // Backfill from linked sales contract where possible.
        $this->execute("\n            UPDATE fg_invoice_payment fip\n            INNER JOIN sales_contract sc ON sc.id = fip.sales_contract_id\n            SET fip.currency_id = sc.currency_id\n            WHERE fip.currency_id IS NULL\n        ");

        $schema = $this->db->schema->getTableSchema('fg_invoice_payment', true);
        $hasIndex = (int) $this->db->createCommand(
            "SELECT COUNT(*) FROM information_schema.statistics
             WHERE table_schema = DATABASE()
               AND table_name = 'fg_invoice_payment'
               AND index_name = 'idx_fg_invoice_payment_currency_id'"
        )->queryScalar() > 0;
        if (!$hasIndex) {
            $this->createIndex(
                'idx_fg_invoice_payment_currency_id',
                'fg_invoice_payment',
                'currency_id'
            );
        }

        $fks = $schema->foreignKeys;
        $hasFk = false;
        foreach ($fks as $fk) {
            if (isset($fk['currency_id'])) {
                $hasFk = true;
                break;
            }
        }

        if (!$hasFk) {
            $this->addForeignKey(
                'fk_fg_invoice_payment_currency_id',
                'fg_invoice_payment',
                'currency_id',
                'currency',
                'id',
                'restrict',
                'restrict'
            );
        }

        // Tighten to NOT NULL only when every row has a value.
        $nullCount = (int) $this->db->createCommand(
            'SELECT COUNT(*) FROM fg_invoice_payment WHERE currency_id IS NULL'
        )->queryScalar();

        if ($nullCount === 0) {
            $this->alterColumn('fg_invoice_payment', 'currency_id', $this->integer()->notNull());
        }
    }

    public function safeDown()
    {
        $schema = $this->db->schema->getTableSchema('fg_invoice_payment', true);
        if ($schema === null) {
            return;
        }

        $columns = array_keys($schema->columns);
        if (!in_array('currency_id', $columns, true)) {
            return;
        }

        $fks = $schema->foreignKeys;
        foreach ($fks as $name => $fk) {
            if (isset($fk['currency_id'])) {
                $this->dropForeignKey($name, 'fg_invoice_payment');
                break;
            }
        }

        $hasIndex = (int) $this->db->createCommand(
            "SELECT COUNT(*) FROM information_schema.statistics
             WHERE table_schema = DATABASE()
               AND table_name = 'fg_invoice_payment'
               AND index_name = 'idx_fg_invoice_payment_currency_id'"
        )->queryScalar() > 0;

        if ($hasIndex) {
            $this->dropIndex('idx_fg_invoice_payment_currency_id', 'fg_invoice_payment');
        }
        $this->dropColumn('fg_invoice_payment', 'currency_id');
    }
}
