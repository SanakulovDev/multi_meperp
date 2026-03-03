<?php
	use yii\db\Migration;

	class m190822_152514_add_price_invoice_detail_table extends Migration{

		public function safeUp(){
			$this->addColumn(
				'{{%invoice_detail}}',
				'price',
				$this->decimal(25, 10)->notNull()->after('qty')
			);
			$this->addColumn(
				'{{%invoice_detail}}',
				'err_sts',
				$this->boolean()->after('price')->comment('Xatolik sababi')
			);
		}

		public function safeDown(){
			$this->dropColumn('{{%invoice_detail}}', 'price');
			$this->dropColumn('{{%invoice_detail}}', 'err_sts');
		}

	}
