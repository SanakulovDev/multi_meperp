<?php
	use yii\db\Migration;

	/**
		* Class m190928_160055_add_contact_dt_fg_invoice
		*/
	class m190928_160055_add_contact_dt_fg_invoice extends Migration{
		/**
			* {@inheritdoc}
			*
			*/
		public function safeUp(){
			$this->addColumn(
				'{{%fg_invoice}}',
				'contract_date',
				$this->date()->after('contract')
			);
		}

	}
