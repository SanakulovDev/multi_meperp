<?php
	use yii\db\Migration;

	class m200326_114500_add_is_main_alias_column_factory_table extends Migration{

		public function safeUp() {
			$this->addColumn('{{%factory}}',
			                 'is_main',
			                 $this->tinyInteger(1)->unsigned()->after('head')
			);
			$this->addColumn('{{%factory}}',
			                 'alias',
			                 $this->string(10)->null()->defaultValue(null)->after('head')
			);
			$this->createIndex('factory_alias', '{{%factory}}', ['alias'], true);
		}
	}