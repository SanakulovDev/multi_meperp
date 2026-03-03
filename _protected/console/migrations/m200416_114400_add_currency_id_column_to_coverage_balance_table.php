<?php
	use yii\db\Migration;

	class m200416_114400_add_currency_id_column_to_coverage_balance_table extends Migration{

		public function safeUp(){

			$this->addColumn(
				'{{%coverage_balance}}', 'currency_id',
				$this->integer()
				     ->after('payment_term_id')
			);

			$this->addForeignKey('frk-coverage_balance-currency_id',
                             '{{%coverage_balance}}', 'currency_id',
														 '{{%currency}}', 'id',
														 'CASCADE', 'CASCADE'
        );

		}

		public function safeDown(){

			$this->dropForeignKey('frk-coverage_balance-currency_id','{{%coverage_balance}}');
				
			$this->dropColumn('{{%coverage_balance}}', 'currency_id');

			

		}

	}
