<?php
	use yii\db\Migration;

	/**
		* Handles adding level to table `{{%part_part_wide}}`.
		*/
	class m190813_135000_add_comment_columns_to_part_table extends Migration{
		/**
			* {@inheritdoc}
			*/
		public function safeUp(){
			$this->addColumn('{{%part}}', 'comment', $this->text());
			$this->addColumn('{{%part}}', 'commented_by', $this->integer());
			$this->addColumn('{{%part}}', 'commented_at', $this->dateTime());
			$this->addForeignKey('fk_part_commented_by',
			                     '{{%part}}', 'commented_by',
			                     '{{%user}}', 'id'
			);
		}

		/**
			* {@inheritdoc}
			*/
		public function safeDown(){
			$this->dropForeignKey('fk_part_commented_by', '{{%part}}');
			$this->dropColumn('{{%part}}', 'comment');
			$this->dropColumn('{{%part}}', 'commented_by');
			$this->dropColumn('{{%part}}', 'commented_at');
		}
	}
