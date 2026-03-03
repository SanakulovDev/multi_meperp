<?php
	use yii\db\Migration;

	class m190930_141500_add_act_access_user_table extends Migration{

		public function safeUp(){
			$this->addColumn(
				'{{%user}}',
				'act_access',
				$this->tinyInteger(1)->null()->defaultValue(0)->after('status')
			);
		}

		public function safeDown(){
			$this->dropColumn('{{%user}}', 'act_access');
		}

	}
