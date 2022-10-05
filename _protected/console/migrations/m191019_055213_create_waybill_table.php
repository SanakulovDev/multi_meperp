<?php
	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%waybill}}`.
	 */
	class m191019_055213_create_waybill_table extends Migration{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable('{{%waybill}}', [
				'id' => $this->primaryKey(),
				'factory_id' => $this->integer(11)->notNull(),
				'waybill_no' => $this->string(100)->notNull()->comment('Increment by factory and year(waybill_date)'),
				'waybill_date' => $this->date()->notNull(),
				'asn' => $this->string(100)->null()->defaultValue(null),
				'driver' => $this->string(100)->null()->defaultValue(null),
				'truck' => $this->string(30)->null()->defaultValue(null),
				'manager' => $this->string(100)->null()->defaultValue(null),
				'account' => $this->string(100)->null()->defaultValue(null),
				'sender' => $this->string(100)->null()->defaultValue(null),
				'created_at' => $this->integer(11)->notNull(),
				'created_by' => $this->integer(11)->null(),
				'updated_at' => $this->integer(11)->null()->defaultValue(null),
				'updated_by' => $this->integer(11)->null()->defaultValue(null),
			], $tableOptions);
			$this->addForeignKey('fk_waybill_factory_id',
													 '{{%waybill}}', 'factory_id',
													 '{{%factory}}', 'id',
													 'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_waybill_created_by',
													 '{{%waybill}}', 'created_by',
													 '{{%user}}', 'id',
													 'SET NULL', 'SET NULL'
			);
			$this->addForeignKey('fk_waybill_updated_by',
													 '{{%waybill}}', 'updated_by',
													 '{{%user}}', 'id',
													 'SET NULL', 'SET NULL'
			);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown(){
			$this->dropForeignKey('fk_waybill_factory_id', '{{%waybill}}');
			$this->dropForeignKey('fk_waybill_created_by', '{{%waybill}}');
			$this->dropForeignKey('fk_waybill_updated_by', '{{%waybill}}');
			$this->dropTable('{{%waybill}}');
		}
	}
