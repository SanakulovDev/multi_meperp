<?php
	use yii\db\Migration;

	class m191021_155500_add_comment_columns_to_fg_invoice_tables extends Migration{

		public function safeUp(){
			$this->addColumn('{{%fg_invoice}}', 'comment', $this->string(255));
		}

		public function safeDown(){
			$this->dropColumn('{{%fg_invoice}}', 'comment');
		}
	}
