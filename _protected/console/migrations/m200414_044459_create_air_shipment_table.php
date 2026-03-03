<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%air_shipment}}`.
 */
class m200414_044459_create_air_shipment_table extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
		$this->createTable('{{%air_shipment_reason}}', [
			'id' => $this->primaryKey(),
			'title' => $this->string(191)->notNull(),
			'created_at' => $this->integer(11)->notNull(),
			'created_by' => $this->integer(11)->null(),
			'updated_at' => $this->integer(11)->null()->defaultValue(null),
			'updated_by' => $this->integer(11)->null()->defaultValue(null),
		], $tableOptions);

		$this->createTable('{{%air_shipment}}', [
			'id' => $this->primaryKey(10)->unsigned(),
			'supplier_id' => $this->integer(11)->notNull(),
			'volume' => $this->decimal(25, 10)->notNull(),
			'cost' => $this->decimal(25, 10)->notNull(),
			'period' => $this->string(10)->notNull(),
			'air_shipment_reason_id' => $this->integer(11)->null(),
			'remark' => $this->string(191)->null()->defaultValue(null),
			'created_by' => $this->integer(11)->null(),
			'created_at' => $this->integer(11)->notNull(),
			'updated_by' => $this->integer(11)->null()->defaultValue(null),
			'updated_at' => $this->integer(11)->null()->defaultValue(null),
		], $tableOptions);

		$this->addForeignKey(
			'fk_air_shipment_supplier_id',
			'{{%air_shipment}}',
			'supplier_id',
			'{{%supplier}}',
			'id',
			'CASCADE',
			'CASCADE'
		);

		$this->addForeignKey(
			'fk_air_shipment_reason_id',
			'{{%air_shipment}}',
			'air_shipment_reason_id',
			'{{%air_shipment_reason}}',
			'id',
			'SET NULL',
			'SET NULL'
		);

		$this->addForeignKey(
			'fk_air_shipment_created_by',
			'{{%air_shipment}}',
			'created_by',
			'{{%user}}',
			'id',
			'SET NULL',
			'SET NULL'
		);
		$this->addForeignKey(
			'fk_air_shipment_updated_by',
			'{{%air_shipment}}',
			'updated_by',
			'{{%user}}',
			'id',
			'SET NULL',
			'SET NULL'
		);

		// create permissions
		Yii::$app->db->createCommand(
			"INSERT IGNORE `auth_item`(`name`, `type`) 
                      VALUES 
                      ('air-shipment-reason-create',2),
                      ('air-shipment-reason-delete',2),
                      ('air-shipment-reason-index',2),
                      ('air-shipment-reason-update',2),
                      ('air-shipment-create',2),
                      ('air-shipment-delete',2),
                      ('air-shipment-index',2),
                      ('air-shipment-update',2),
                      ('air-shipment-xls',2);

            INSERT IGNORE `auth_item_child`(`parent`, `child`) 
                      VALUES 
                      ('superadmin','air-shipment-reason-create'),
                      ('superadmin','air-shipment-reason-delete'),
                      ('superadmin','air-shipment-reason-index'),
                      ('superadmin','air-shipment-reason-update'),
                      ('superadmin','air-shipment-create'),
                      ('superadmin','air-shipment-delete'),
                      ('superadmin','air-shipment-index'),
                      ('superadmin','air-shipment-update'),
                      ('superadmin','air-shipment-xls'),
											('admin','air-shipment-create'),
                      ('admin','air-shipment-delete'),
                      ('admin','air-shipment-index'),
                      ('admin','air-shipment-update'),
                      ('admin','air-shipment-xls'),											
											('mfu','air-shipment-create'),
                      ('mfu','air-shipment-delete'),
                      ('mfu','air-shipment-index'),
                      ('mfu','air-shipment-update'),
                      ('mfu','air-shipment-xls');

            INSERT INTO `air_shipment_reason`(`title`, `created_at`) 
                    VALUES
                    ('Увеличение плана производства АО \"УзАвтоМоторс\"', 1586849115),
                    ('Влияние пандемии COVID-19 на своевременную поставку', 1586849115),
                    ('Задержка отгрузки из-за несвоевременной оплаты на поставку', 1586849115),
                    ('Задержка груза на границах тарнзитных стран из-за конфликтных ситуаций', 1586849115),
                    ('Несвоевременный заказ ответственным поставками', 1586849115),
                    ('Из-за неправильной учетности остатка на складе и предоставления неправильного баланса', 1586849115),
                    ('Из-за несвоевременной планирования перевозки или несвоевременный заказ логиста.', 1586849115),
                    ('Из-за перерасхода продукции на производстве', 1586849115),
                    ('Из-за брака продукции', 1586849115),
                    ('Из-за поломки оборудования и срочности доставки запасных частей', 1586849115),
                    ('Предусмотренное авиа, т.е. в контракте указана только доставка авиа перевозкой', 1586849115),
                    ('Авиа перевозка новых проектов, испытание продукции (образцы)', 1586849115);
           
            "
		)->execute();
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('{{%air_shipment}}');
		$this->dropTable('{{%air_shipment_reason}}');
	}
}
