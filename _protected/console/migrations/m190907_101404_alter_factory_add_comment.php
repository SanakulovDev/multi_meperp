<?php
	use app\models\Warehouse;
	use yii\db\Migration;

	/**
		* Class m190907_101404_alter_factory_add_comment
		*/
	class m190907_101404_alter_factory_add_comment extends Migration{
		/**
			* {@inheritdoc}
			*/
		public function safeUp(){
			$this->addColumn('{{%factory}}', 'remark', $this->string(255)->after('duns'));
			$this->addColumn('{{%warehouse}}', 'is_coverable', $this->boolean()->defaultValue(Warehouse::COVERABLE_NO)->after('description'));
		}

		/**
			* {@inheritdoc}
			*/
		public function safeDown(){
			$this->dropColumn('{{%factory}}', 'remark');
			$this->dropColumn('{{%warehouse}}', 'is_coverable');
		}

	}
