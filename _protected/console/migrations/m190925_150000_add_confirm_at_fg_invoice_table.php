<?php
	use yii\db\Migration;

	class m190925_150000_add_confirm_at_fg_invoice_table extends Migration{

		public function safeUp(){
			$this->addColumn(
				'{{%fg_invoice}}',
				'confirmed_at',
				$this->integer(11)->null()->defaultValue(null)
			);
			$this->addColumn(
				'{{%fg_invoice}}',
				'confirmed_by',
				$this->integer(11)->null()->defaultValue(null)->after('confirmed_at')
			);
			$this->addForeignKey('fk_fg_invoice_confirmed_by',
			                     '{{%fg_invoice}}', 'confirmed_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
		}

	}
