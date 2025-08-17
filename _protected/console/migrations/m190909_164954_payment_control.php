<?php
	use yii\db\Migration;

	class m190909_164954_payment_control extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable('{{%payment_control}}', [
				'id' => $this->primaryKey(11),
				'no' => $this->string(100)->notNull(),
				'date' => $this->date()->notNull(),
				'payment_type_id' => $this->integer(11)->notNull(),
				'amount' => $this->decimal(25, 10)->notNull(),
				'contract_id' => $this->integer(11)->notNull(),
				'supplier_id' => $this->integer(11)->notNull(),
				'created_at' => $this->integer(11)->notNull(),
				'created_by' => $this->integer(11)->notNull(),
				'updated_by' => $this->integer(11)->null()->defaultValue(null),
				'updated_at' => $this->integer(11)->null()->defaultValue(null),
			], $tableOptions);
			$this->createIndex('uk_no_date', '{{%payment_control}}', ['no', 'date'], true);
			$this->addForeignKey(
				'fk_payment_control_contract_id',
				'{{%payment_control}}', 'contract_id',
				'{{%contract}}', 'id',
				'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey(
				'fk_payment_control_supplier_id',
				'{{%payment_control}}', 'supplier_id',
				'{{%supplier}}', 'id',
				'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey(
				'fk_payment_control_pay_type',
				'{{%payment_control}}', 'payment_type_id',
				'{{%payment_type}}', 'id',
				'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey(
				'fk_payment_control_created_by',
				'{{%payment_control}}', 'created_by',
				'{{%user}}', 'id',
				'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey(
				'fk_payment_control_updated_by',
				'{{%payment_control}}', 'updated_by',
				'{{%user}}', 'id',
				'RESTRICT', 'RESTRICT'
			);
		}

		public function safeDown(){
			$this->dropForeignKey('fk_payment_control_contract_id', '{{%payment_control}}');
			$this->dropForeignKey('fk_payment_control_supplier_id', '{{%payment_control}}');
			$this->dropForeignKey('fk_payment_control_payment_type_id', '{{%payment_control}}');
			$this->dropForeignKey('fk_payment_control_created_by', '{{%payment_control}}');
			$this->dropForeignKey('fk_payment_control_updated_by', '{{%payment_control}}');
			$this->dropIndex('uk_no_date', '{{%payment_control}}');
			$this->dropTable('{{%payment_control}}');
		}
	}
