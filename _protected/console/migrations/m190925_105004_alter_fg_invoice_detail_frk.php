<?php
	use yii\db\Migration;

	class m190925_105004_alter_fg_invoice_detail_frk extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$this->dropForeignKey('frk-fg_invoice_detail-fg_invoice_id', '{{%fg_invoice_detail}}');
			$this->addForeignKey('frk-fg_invoice_detail-fg_invoice_id',
			                     '{{%fg_invoice_detail}}', ['fg_invoice_id'],
			                     'fg_invoice', 'id',
			                     'CASCADE', 'RESTRICT'
			);
		}
	}
