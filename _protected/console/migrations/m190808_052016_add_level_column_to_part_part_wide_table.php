<?php
	use yii\db\Migration;

	/**
		* Handles adding level to table `{{%part_part_wide}}`.
		*/
	class m190808_052016_add_level_column_to_part_part_wide_table extends Migration{
		/**
			* {@inheritdoc}
			*/
		public function safeUp(){
			$this->addColumn('{{%part_part_wide}}', 'level', $this->integer());
		}

		/**
			* {@inheritdoc}
			*/
		public function safeDown(){
			$this->dropColumn('{{%part_part_wide}}', 'level');
		}
	}
