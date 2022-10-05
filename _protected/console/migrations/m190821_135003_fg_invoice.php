<?php
	use yii\db\Migration;

	class m190821_135003_fg_invoice extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%fg_invoice}}',
				[
					'id' => $this->primaryKey(11),
					'factory_id' => $this->integer(11)->notNull(),
					'invoice_no' => $this->string(100)->notNull()->comment('Increment by factory and year(invoice_date)'),
					'invoice_date' => $this->date()->notNull(),
					'customer_id' => $this->integer(11)->notNull(),
					'contract' => $this->string(50)->null()->defaultValue(null)->comment('Sales contract of Customer'),
					'doveronnost' => $this->string(50)->notNull(),
					'driver' => $this->string(100)->null()->defaultValue(null),
					'truck' => $this->string(30)->null()->defaultValue(null),
					'manager' => $this->string(100)->null()->defaultValue(null),
					'account' => $this->string(100)->null()->defaultValue(null),
					'sender' => $this->string(100)->null()->defaultValue(null),
					'created_at' => $this->integer(11)->notNull(),
					'created_by' => $this->integer(11)->notNull(),
					'updated_at' => $this->integer(11)->null()->defaultValue(null),
					'updated_by' => $this->integer(11)->null()->defaultValue(null),
				], $tableOptions
			);
			$this->addForeignKey('fk_fg_invoice_customer_id',
			                     '{{%fg_invoice}}', 'customer_id',
			                     '{{%customer}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_fg_invoice_factory_id',
			                     '{{%fg_invoice}}', 'factory_id',
			                     '{{%factory}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_fg_invoice_created_by',
			                     '{{%fg_invoice}}', 'created_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_fg_invoice_updated_by',
			                     '{{%fg_invoice}}', 'updated_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
		}

		public function safeDown(){
			$this->dropForeignKey('fk_fg_invoice_customer_id', '{{%fg_invoice}}');
			$this->dropForeignKey('fk_fg_invoice_factory_id', '{{%fg_invoice}}');
			$this->dropForeignKey('fk_fg_invoice_created_by', '{{%fg_invoice}}');
			$this->dropForeignKey('fk_fg_invoice_updated_by', '{{%fg_invoice}}');
			$this->dropTable('{{%fg_invoice}}');
		}
	}
