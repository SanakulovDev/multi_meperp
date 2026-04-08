<?php

use yii\db\Migration;

/**
 * Changes fg_invoice_payment from many-to-many (pivot) to one-to-one with waybill.
 * Drops fg_invoice_payment_waybill pivot table.
 * Adds waybill_id column directly to fg_invoice_payment.
 */
class m260406_000001_fg_invoice_payment_one_to_one extends Migration
{
    public function safeUp()
    {
        // Drop pivot table if it exists (may be absent due to prior partial migration)
        $tables = $this->db->schema->getTableNames();
        if (in_array('fg_invoice_payment_waybill', $tables, true)) {
            $this->dropForeignKey('fk_fgipw_waybill_id', 'fg_invoice_payment_waybill');
            $this->dropForeignKey('fk_fgipw_payment_id', 'fg_invoice_payment_waybill');
            $this->dropTable('fg_invoice_payment_waybill');
        }

        // Add direct FK column (idempotent: skip if already exists)
        $columns = array_keys($this->db->schema->getTableSchema('fg_invoice_payment')->columns);
        if (!in_array('waybill_id', $columns, true)) {
            $this->addColumn('fg_invoice_payment', 'waybill_id', $this->integer()->null()->defaultValue(null)->after('sales_contract_id'));
        } else {
            // Column already exists with default 0 — change to NULL-able for FK compatibility
            $this->alterColumn('fg_invoice_payment', 'waybill_id', $this->integer()->null()->defaultValue(null));
            $this->execute("UPDATE fg_invoice_payment SET waybill_id = NULL WHERE waybill_id = 0");
        }

        // Add FK only if not already present
        $fks = $this->db->schema->getTableSchema('fg_invoice_payment', true)->foreignKeys;
        $hasFk = false;
        foreach ($fks as $fk) {
            if (isset($fk['waybill_id'])) { $hasFk = true; break; }
        }
        if (!$hasFk) {
            $this->addForeignKey(
                'fk_fg_invoice_payment_waybill',
                'fg_invoice_payment',
                'waybill_id',
                'waybill',
                'id',
                'restrict',
                'cascade'
            );
        }
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_fg_invoice_payment_waybill', 'fg_invoice_payment');
        $this->dropColumn('fg_invoice_payment', 'waybill_id');

        $this->createTable('fg_invoice_payment_waybill', [
            'id'         => $this->primaryKey(),
            'payment_id' => $this->integer()->notNull(),
            'waybill_id' => $this->integer()->notNull(),
        ]);
        $this->addForeignKey('fk_fgipw_payment', 'fg_invoice_payment_waybill', 'payment_id', 'fg_invoice_payment', 'id', 'cascade', 'cascade');
        $this->addForeignKey('fk_fgipw_waybill', 'fg_invoice_payment_waybill', 'waybill_id', 'waybill', 'id', 'restrict', 'cascade');
    }
}
