<?php

use yii\db\Migration;

/**
 * Class m200505_090057_product_specification_table
 */
class m200505_090057_product_specification_table extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$tableOptions = 'ENGINE=InnoDB';
		$this->createTable('{{%product_specification}}', [
			'id' => $this->primaryKey(),
			'code' => $this->string(50)->notNull(),
			'part_id' => $this->integer(11)->notNull(),
			'description' => $this->string(191)->null(),
			'status' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(1)->comment('1-active; 0-inactive;'),
			'updated_by' => $this->integer(11),
			'updated_at' => $this->integer(11),
		], $tableOptions);

		$this->addForeignKey(
			'fk_product_specification_part_id',
			'{{%product_specification}}',
			'part_id',
			'{{%part}}',
			'id',
			'RESTRICT',
			'RESTRICT'
		);

		$this->createTable('{{%product_specification_item}}', [
			'id' => $this->primaryKey(),
			'product_specification_id' => $this->integer(11)->notNull(),
			'part_id' => $this->integer(11)->notNull(),
			'usage_qty' => $this->decimal(25, 10)->notNull()->defaultValue('1.00000'),
			'warehouse_id' => $this->integer(11)
		], $tableOptions);

		$this->addForeignKey(
			'fk_product_specification_item_spec_id',
			'{{%product_specification_item}}',
			'product_specification_id',
			'{{%product_specification}}',
			'id',
			'CASCADE',
			'CASCADE'
		);

		$this->addForeignKey(
			'fk_product_specification_item_part_id',
			'{{%product_specification_item}}',
			'part_id',
			'{{%part}}',
			'id',
			'RESTRICT',
			'RESTRICT'
		);

		Yii::$app->db->createCommand(
			"INSERT IGNORE `auth_item`(`name`, `type`) 
								VALUES 
								('product-specification-index',2),
								('product-specification-create',2),
								('product-specification-update',2),
								('product-specification-delete',2),
								('product-specification-xls',2);
			 INSERT IGNORE `auth_item_child`(`parent`, `child`) 
								VALUES 
								('pe', 'product-specification-index'),
								('pe', 'product-specification-create'),
								('pe', 'product-specification-update'),
								('pe', 'product-specification-delete'),
								('pe', 'product-specification-xls'),
								('superadmin', 'product-specification-index'),
								('superadmin', 'product-specification-create'),
								('superadmin', 'product-specification-update'),
								('superadmin', 'product-specification-delete'),
								('superadmin', 'product-specification-xls'),
								('admin', 'product-specification-index'),
								('admin', 'product-specification-create'),
								('admin', 'product-specification-update'),
								('admin', 'product-specification-delete'),
								('admin', 'product-specification-xls'),
								('observer', 'product-specification-index'),
								('observer', 'product-specification-xls'),
								('plan', 'product-specification-index'),
								('plan', 'product-specification-xls'),
								('buyer', 'product-specification-index'),
								('buyer', 'product-specification-xls'),
								('counter', 'product-specification-index'),
								('counter', 'product-specification-xls'),
								('mfu', 'product-specification-index'),
								('mfu', 'product-specification-xls'),
								('mrp', 'product-specification-index'),
								('mrp', 'product-specification-xls'),
								('mrp-logx', 'product-specification-index'),
								('mrp-logx', 'product-specification-xls'),
								('sales', 'product-specification-index'),
								('sales', 'product-specification-xls'),
								('shipper', 'product-specification-index'),
								('shipper', 'product-specification-xls');
			"
		)->execute();

		Yii::$app->authManager->invalidateCache();
		$this->addColumn('{{%payment_control}}', 'part_order_id', $this->integer(11)->null()->after('orders'));
		Yii::$app->db->createCommand('UPDATE payment_control SET part_order_id=CAST(orders AS SIGNED) WHERE orders IS NOT NULL')->execute();
		$this->dropColumn('{{%payment_control}}', 'orders');
		// $this->alterColumn('','')
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropForeignKey('fk_product_specification_item_spec_id', '{{%product_specification_item}}');
		$this->dropForeignKey('fk_product_specification_item_part_id', '{{%product_specification_item}}');
		$this->dropTable('{{%product_specification_item}}');

		$this->dropForeignKey('fk_product_specification_part_id', '{{%product_specification}}');
		$this->dropTable('{{%product_specification}}');
		
		Yii::$app->authManager->invalidateCache();

		$this->addColumn('{{%payment_control}}', 'orders', $this->string(100)->null()->after('part_order_id'));
		Yii::$app->db->createCommand('UPDATE payment_control SET orders=CAST(part_order_id as CHAR(10)) WHERE part_order_id IS NOT NULL')->execute();
		$this->dropColumn('{{%payment_control}}', 'part_order_id');
	}
}
