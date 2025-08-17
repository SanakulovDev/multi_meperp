<?php
	use yii\db\Migration;

	/**
	 * Handles adding level to table `{{%part_part_wide}}`.
	 */
	class m200310_132000_add_comment_columns_to_part_order_detail_table extends Migration{
		public function safeUp(){
			$this->addColumn(
				'{{%part_order_detail}}', 'comment',
				$this->string(255)->null()->defaultValue(null)->after('exwrk_actual')
			);
		}

		public function safeDown(){
			$this->dropColumn('{{%part_order_detail}}', 'comment');
		}
	}
