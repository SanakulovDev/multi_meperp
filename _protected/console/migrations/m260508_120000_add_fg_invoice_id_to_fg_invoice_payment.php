<?php

use yii\db\Migration;

/**
 * Links FG invoice payments directly to fg_invoice.
 */
class m260508_120000_add_fg_invoice_id_to_fg_invoice_payment extends Migration
{
    public function safeUp()
    {
        $schema = $this->db->schema->getTableSchema('fg_invoice_payment', true);
        if ($schema === null) {
            return;
        }

        $columns = array_keys($schema->columns);
        if (!in_array('fg_invoice_id', $columns, true)) {
            $this->addColumn(
                'fg_invoice_payment',
                'fg_invoice_id',
                $this->integer()->null()->defaultValue(null)->after('waybill_id')
            );
        }

        $this->execute("
            UPDATE fg_invoice_payment fip
            INNER JOIN fg_invoice_waybill fiw ON fiw.waybill_id = fip.waybill_id
            INNER JOIN fg_invoice fi ON fi.id = fiw.fg_invoice_id
            LEFT JOIN sales_contract sc ON sc.id = fip.sales_contract_id
            SET fip.fg_invoice_id = fi.id
            WHERE fip.fg_invoice_id IS NULL
              AND fip.waybill_id IS NOT NULL
              AND (
                    fip.sales_contract_id IS NULL
                    OR (fi.contract = sc.contract_no AND fi.customer_id = sc.customer_id)
                  )
        ");

        $hasIndex = (int) $this->db->createCommand(
            "SELECT COUNT(*) FROM information_schema.statistics
             WHERE table_schema = DATABASE()
               AND table_name = 'fg_invoice_payment'
               AND index_name = 'idx_fg_invoice_payment_fg_invoice_id'"
        )->queryScalar() > 0;
        if (!$hasIndex) {
            $this->createIndex(
                'idx_fg_invoice_payment_fg_invoice_id',
                'fg_invoice_payment',
                'fg_invoice_id'
            );
        }

        $schema = $this->db->schema->getTableSchema('fg_invoice_payment', true);
        $hasFk = false;
        foreach ($schema->foreignKeys as $fk) {
            if (isset($fk['fg_invoice_id'])) {
                $hasFk = true;
                break;
            }
        }

        if (!$hasFk) {
            $this->addForeignKey(
                'fk_fg_invoice_payment_fg_invoice_id',
                'fg_invoice_payment',
                'fg_invoice_id',
                'fg_invoice',
                'id',
                'restrict',
                'cascade'
            );
        }
    }

    public function safeDown()
    {
        $schema = $this->db->schema->getTableSchema('fg_invoice_payment', true);
        if ($schema === null || !isset($schema->columns['fg_invoice_id'])) {
            return;
        }

        foreach ($schema->foreignKeys as $name => $fk) {
            if (isset($fk['fg_invoice_id'])) {
                $this->dropForeignKey($name, 'fg_invoice_payment');
                break;
            }
        }

        $hasIndex = (int) $this->db->createCommand(
            "SELECT COUNT(*) FROM information_schema.statistics
             WHERE table_schema = DATABASE()
               AND table_name = 'fg_invoice_payment'
               AND index_name = 'idx_fg_invoice_payment_fg_invoice_id'"
        )->queryScalar() > 0;
        if ($hasIndex) {
            $this->dropIndex('idx_fg_invoice_payment_fg_invoice_id', 'fg_invoice_payment');
        }

        $this->dropColumn('fg_invoice_payment', 'fg_invoice_id');
    }
}
