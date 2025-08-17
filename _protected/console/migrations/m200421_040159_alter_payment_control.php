<?php

use app\models\PaymentControl;
use yii\db\Migration;

/**
 * Class m200421_040159_alter_payment_control
 */
class m200421_040159_alter_payment_control extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // $this->dropColumn('{{%payment_control}}', 'payment_type_id');
        $this->addColumn('{{%payment_control}}', 'payment_type', $this->tinyInteger(1)->defaultValue(PaymentControl::LC_TYPE));
        $this->addColumn('{{%payment_control}}', 'expire_date', $this->date()->null());
        $this->addColumn('{{%payment_control}}', 'shipment_date', $this->date()->null());
        $this->addColumn('{{%payment_control}}', 'orders', $this->string()->null());
        $this->addColumn('{{%payment_control}}', 'bank_name', $this->string(191)->null());
        $this->addColumn('{{%payment_control}}', 'is_spend', $this->boolean()->defaultValue(false));
        $this->alterColumn('{{%payment_control}}', 'payment_type_id', $this->integer(11)->null());
        $this->alterColumn('{{%payment_control}}', 'contract_id', $this->integer(11)->null());
        
        $tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        $this->createTable('{{%invoice_payment}}', [
			'id' => $this->primaryKey(),
			'invoice_id' => $this->integer(11)->notNull(),
			'payment_control_id' => $this->integer(11)->notNull(),
			'amount' => $this->decimal(20,5)->notNull()
        ], $tableOptions);

        $this->addForeignKey('frk-invoice_payment-invoice_id',
                             '{{%invoice_payment}}', 
                             'invoice_id',
                            '{{%invoice}}', 'id',
                            'CASCADE', 'CASCADE');
        
        $this->addForeignKey('frk-invoice_payment-payment_control_id',
                            '{{%invoice_payment}}', 
                            'payment_control_id',
                           '{{%payment_control}}', 'id',
                           'CASCADE', 'CASCADE');                    
        
        $this->addColumn('{{%invoice}}', 'is_payed', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%invoice}}', 'is_payed');

        $this->dropColumn('{{%payment_control}}', 'payment_type');
        $this->dropColumn('{{%payment_control}}', 'expire_date');
        $this->dropColumn('{{%payment_control}}', 'shipment_date');
        $this->dropColumn('{{%payment_control}}', 'orders');
        $this->dropColumn('{{%payment_control}}', 'bank_name');
        $this->dropColumn('{{%payment_control}}', 'is_spend');

        $this->dropForeignKey('frk-invoice_payment-payment_control_id','{{%invoice_payment}}');
        $this->dropForeignKey('frk-invoice_payment-invoice_id','{{%invoice_payment}}');

        $this->dropTable('{{%invoice_payment}}');
    }
}
