<?php
	use yii\db\Migration;

	class m190910_115103_add_column_payment_invoice_table extends Migration{

		public function safeUp(){
			$this->addColumn('{{%invoice}}',
			                 'invoice_date',
			                 $this->date()->null()->defaultValue(null)->after('currency_id')
			);
			$this->addColumn('{{%invoice}}',
			                 'invoice_amount',
			                 $this->decimal(25, 10)->null()->defaultValue(null)->after('invoice_date')
			);
			$this->addColumn('{{%invoice}}',
			                 'payment_control_id',
			                 $this->integer(11)->null()->defaultValue(null)->after('invoice_amount')
			);
			$this->addForeignKey(
				'fk_invoice_payment_control_id', '{{%invoice}}', 'payment_control_id',
				'{{%payment_control}}', 'id',
				'RESTRICT', 'RESTRICT'
			);
		}

		public function safeDown(){
			$this->dropForeignKey('fk_invoice_payment_control_id', '{{%invoice}}');
			$this->dropColumn('{{%invoice}}', 'payment_control_id');
			$this->dropColumn('{{%invoice}}', 'invoice_amount');
			$this->dropColumn('{{%invoice}}', 'invoice_date');
		}

	}
