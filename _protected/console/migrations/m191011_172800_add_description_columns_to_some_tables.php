<?php
	use yii\db\Migration;

	class m191011_172800_add_description_columns_to_some_tables extends Migration{

		public function safeUp(){
			$this->addColumn('{{%consolidation_type}}', 'description', $this->string());
			$this->addColumn('{{%part_type}}', 'description', $this->string());
			$this->addColumn('{{%product_model}}', 'description', $this->string());
			$this->addColumn('{{%product_line}}', 'description', $this->string());
			$this->addColumn('{{%delivery_term}}', 'description', $this->string());
			$this->addColumn('{{%payment_term}}', 'description', $this->string());
			$this->addColumn('{{%contract_subject}}', 'description', $this->string());
			$this->addColumn('{{%contract_source}}', 'description', $this->string());
		}

		public function safeDown(){
			$this->dropColumn('{{%consolidation_type}}', 'description');
			$this->dropColumn('{{%part_type}}', 'description');
			$this->dropColumn('{{%product_model}}', 'description');
			$this->dropColumn('{{%product_line}}', 'description');
			$this->dropColumn('{{%delivery_term}}', 'description');
			$this->dropColumn('{{%payment_term}}', 'description');
			$this->dropColumn('{{%contract_subject}}', 'description');
			$this->dropColumn('{{%contract_source}}', 'description');
		}
	}
