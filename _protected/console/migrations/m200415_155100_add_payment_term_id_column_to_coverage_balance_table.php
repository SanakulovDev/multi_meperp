<?php
	use yii\db\Migration;

	class m200415_155100_add_payment_term_id_column_to_coverage_balance_table extends Migration{

		public function safeUp(){

			Yii::$app->db->createCommand()->truncateTable('coverage_balance')->execute();

			$this->addColumn(
				'{{%coverage_balance}}', 'payment_term_id',
				$this->integer()
				     ->notNull()
				     ->after('supplier_id')
			);

			$this->addForeignKey('frk-coverage_balance-payment_term_id',
                             '{{%coverage_balance}}', 'payment_term_id',
														 '{{%payment_term}}', 'id',
														 'CASCADE', 'CASCADE'
        );

		}

		public function safeDown(){

			$this->dropForeignKey('frk-coverage_balance-payment_term_id','{{%coverage_balance}}');
				
			$this->dropColumn('{{%coverage_balance}}', 'payment_term_id');

			

		}

	}
