<?php

use yii\db\Migration;

/**
 * Class m200422_105749_drop_payment_type
 */
class m200422_105749_drop_payment_type extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		// create permission and assignments
		Yii::$app->db->createCommand(
            "INSERT IGNORE `auth_item`(`name`, `type`) 
                VALUES 
                ('invoice-payment-create',2),
                ('invoice-payment-delete',2),
                ('invoice-payment-index',2),
                ('invoice-payment-update',2),
                ('invoice-payment-xls',2),
                ('invoice-create',2),
                ('invoice-delete',2),
                ('invoice-index',2),
                ('invoice-update',2),
                ('invoice-xls',2),
                ('payment-control-xls',2);

            INSERT IGNORE `auth_item_child`(`parent`, `child`) 
                VALUES 
                ('superadmin','payment-control-xls'),
                ('admin','payment-control-xls'),
                ('superadmin','invoice-payment-create'),
                ('superadmin','invoice-payment-delete'),
                ('superadmin','invoice-payment-index'),
                ('superadmin','invoice-payment-update'),
                ('superadmin','invoice-payment-xls'),
                ('admin','invoice-payment-create'),
                ('admin','invoice-payment-delete'),
                ('admin','invoice-payment-index'),
                ('admin','invoice-payment-update'),
                ('admin','invoice-payment-xls'),
                ('superadmin','invoice-create'),
                ('superadmin','invoice-delete'),
                ('superadmin','invoice-index'),
                ('superadmin','invoice-update'),
                ('superadmin','invoice-xls'),
                ('admin','invoice-create'),
                ('admin','invoice-delete'),
                ('admin','invoice-index'),
                ('admin','invoice-update'),
                ('admin','invoice-xls'),
                ('observer','invoice-payment-index'),
                ('observer','oem-plan-index');

            DELETE FROM `auth_item_child` WHERE `parent`='observer' AND `child` IN ('payment-type-index','payment-type-view');   
            "
        )->execute();

        $this->addColumn('{{%invoice_payment}}', 'updated_at', $this->integer(11)->notNull());
        $this->addColumn('{{%invoice_payment}}', 'updated_by', $this->integer(11)->notNull());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%invoice_payment}}', 'updated_at');
        $this->dropColumn('{{%invoice_payment}}', 'updated_by');
	}
}
