<?php
use yii\db\Migration;

/**
 * Handles the creation of tables `{{%fg_invoice_payment}}` and `{{%fg_invoice_payment_waybill}}`.
 */
class m260406_000000_create_fg_invoice_payment_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';

        $this->createTable('{{%fg_invoice_payment}}', [
            'id'                => $this->primaryKey(11),
            'no'                => $this->string(100)->notNull(),
            'date'              => $this->date()->notNull(),
            'sales_contract_id' => $this->integer(11)->notNull(),
            'amount'            => $this->decimal(25, 10)->notNull(),
            'created_at'        => $this->integer(11)->notNull(),
            'created_by'        => $this->integer(11)->notNull(),
            'updated_by'        => $this->integer(11)->null()->defaultValue(null),
            'updated_at'        => $this->integer(11)->null()->defaultValue(null),
        ], $tableOptions);

        $this->addForeignKey(
            'fk_fg_invoice_payment_sales_contract_id',
            '{{%fg_invoice_payment}}', 'sales_contract_id',
            '{{%sales_contract}}', 'id',
            'restrict', 'restrict'
        );

        $this->createTable('{{%fg_invoice_payment_waybill}}', [
            'id'         => $this->primaryKey(11),
            'payment_id' => $this->integer(11)->notNull(),
            'waybill_id' => $this->integer(11)->notNull(),
        ], $tableOptions);

        $this->addForeignKey(
            'fk_fgipw_payment_id',
            '{{%fg_invoice_payment_waybill}}', 'payment_id',
            '{{%fg_invoice_payment}}', 'id',
            'cascade', 'cascade'
        );
        $this->addForeignKey(
            'fk_fgipw_waybill_id',
            '{{%fg_invoice_payment_waybill}}', 'waybill_id',
            '{{%waybill}}', 'id',
            'restrict', 'restrict'
        );

        // RBAC permissions
        Yii::$app->db->createCommand(
            "INSERT IGNORE `auth_item`(`name`, `type`)
            VALUES
            ('fg-invoice-payment-index',2),
            ('fg-invoice-payment-create',2),
            ('fg-invoice-payment-update',2),
            ('fg-invoice-payment-delete',2),
            ('fg-invoice-payment-xls',2)"
        )->execute();

        Yii::$app->db->createCommand(
            "INSERT IGNORE `auth_item_child`(`parent`, `child`)
            VALUES
            ('admin','fg-invoice-payment-index'),
            ('admin','fg-invoice-payment-create'),
            ('admin','fg-invoice-payment-update'),
            ('admin','fg-invoice-payment-delete'),
            ('admin','fg-invoice-payment-xls'),
            ('superadmin','fg-invoice-payment-index'),
            ('superadmin','fg-invoice-payment-create'),
            ('superadmin','fg-invoice-payment-update'),
            ('superadmin','fg-invoice-payment-delete'),
            ('superadmin','fg-invoice-payment-xls')"
        )->execute();

        Yii::$app->getAuthManager()->invalidateCache();
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_fgipw_waybill_id', '{{%fg_invoice_payment_waybill}}');
        $this->dropForeignKey('fk_fgipw_payment_id', '{{%fg_invoice_payment_waybill}}');
        $this->dropForeignKey('fk_fg_invoice_payment_sales_contract_id', '{{%fg_invoice_payment}}');
        $this->dropTable('{{%fg_invoice_payment_waybill}}');
        $this->dropTable('{{%fg_invoice_payment}}');
    }
}
