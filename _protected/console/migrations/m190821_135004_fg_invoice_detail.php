<?php
	use yii\db\Migration;

	class m190821_135004_fg_invoice_detail extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%fg_invoice_detail}}',
				[
					'id' => $this->primaryKey(11),
					'fg_invoice_id' => $this->integer(11)->notNull(),
					'part_no' => $this->string(50)->notNull(),
					'part_name' => $this->string(100)->notNull(),
					'qty' => $this->decimal(25, 10)->notNull(),
					'price' => $this->decimal(25, 10)->notNull(),
					'vat' => $this->decimal(25, 10)->comment('QQS'),
					'excise' => $this->decimal(25, 10)->comment('Аксиз налог'),
					'unit_id' => $this->integer(11)->notNull(),
					'created_at' => $this->integer(11)->notNull(),
					'created_by' => $this->integer(11)->notNull(),
					'updated_at' => $this->integer(11)->null()->defaultValue(null),
					'updated_by' => $this->integer(11)->null()->defaultValue(null),
				], $tableOptions
			);
			$this->addForeignKey('frk-fg_invoice_detail-fg_invoice_id',
			                     '{{%fg_invoice_detail}}', ['fg_invoice_id'],
			                     'fg_invoice', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('frk-fg_invoice_detail-unit_id',
			                     '{{%fg_invoice_detail}}', ['unit_id'],
			                     'unit', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('frk_fg_invoice_detail_created_by',
			                     '{{%fg_invoice_detail}}', 'created_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('frk_fg_invoice_detail_updated_by',
			                     '{{%fg_invoice_detail}}', 'updated_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
		}

		public function safeDown(){
			$this->dropForeignKey('frk_fg_invoice_detail_created_by', '{{%fg_invoice_detail}}');
			$this->dropForeignKey('frk_fg_invoice_detail_updated_by', '{{%fg_invoice_detail}}');
			$this->dropForeignKey('frk-fg_invoice_detail-unit_id', '{{%fg_invoice_detail}}');
			$this->dropForeignKey('frk-fg_invoice_detail-fg_invoice_id', '{{%fg_invoice_detail}}');
			$this->dropTable('{{%fg_invoice_detail}}');
		}
	}
