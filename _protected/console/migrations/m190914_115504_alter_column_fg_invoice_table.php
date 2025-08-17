<?php
	use yii\db\Migration;

	/**
		* Class m190914_115504_alter_column_fg_invoice_table
		*/
	class m190914_115504_alter_column_fg_invoice_table extends Migration{
		public function safeUp(){
			$this->dropColumn('fg_invoice', 'doveronnost');
			$this->addColumn('fg_invoice', 'rec_person_fullname',
			                 $this->string(100)->null()->defaultValue(null)
			                      ->comment('Doverennost FIO')->after('contract')
			);
			$this->addColumn('fg_invoice', 'rec_person_regno',
			                 $this->string(100)->null()->defaultValue(null)
			                      ->comment('Doverennost RegNo')->after('rec_person_fullname')
			);
			$this->addColumn('fg_invoice', 'vat',
			                 $this->integer(3)->null()->defaultValue(null)
			                      ->comment('QQS % xisobida')
			                      ->after('sender')
			);
			$this->addColumn('fg_invoice', 'excise',
			                 $this->integer(3)->null()->defaultValue(null)
			                      ->comment('Аксиз налог % xisobida')
			                      ->after('vat')
			);
			$this->createIndex('uk_factory_invoice_nodt_customer', '{{%fg_invoice}}',
			                   ['factory_id', 'invoice_no', 'invoice_date', 'customer_id'],
			                   true
			);
			$this->dropColumn('fg_invoice_detail', 'vat');
			$this->dropColumn('fg_invoice_detail', 'excise');
		}
	}
