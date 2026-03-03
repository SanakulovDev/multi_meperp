<?php
	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%fg_invoive_waybill}}`.
	 */
	class m191019_061647_create_fg_invoive_waybill_table extends Migration{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';

			$this->createTable('{{%fg_invoive_waybill}}', [
				'id' => $this->primaryKey(),
				'fg_invoice_id' => $this->integer(11)->notNull(),
				'waybill_id' => $this->integer(11)->notNull(),
			], $tableOptions);

			$this->addForeignKey('fk_fg_invoive_waybill_fg_invoice_id',
													 '{{%fg_invoive_waybill}}', 'fg_invoice_id',
													 '{{%fg_invoice}}', 'id',
													 'CASCADE', 'CASCADE'
			);

			$this->addForeignKey('fk_fg_invoive_waybill_waybill_id',
													 '{{%fg_invoive_waybill}}', 'waybill_id',
													 '{{%waybill}}', 'id',
													 'CASCADE', 'CASCADE'
			);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown(){
			$this->dropForeignKey('fk_fg_invoive_waybill_fg_invoice_id', '{{%fg_invoive_waybill}}');
			$this->dropForeignKey('fk_fg_invoive_waybill_waybill_id', '{{%fg_invoive_waybill}}');
			$this->dropTable('{{%fg_invoive_waybill}}');
		}
	}
