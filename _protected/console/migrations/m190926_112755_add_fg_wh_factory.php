<?php
	use yii\db\Migration;

	/**
		* Class m190926_112755_add_fg_wh_factory
		*/
	class m190926_112755_add_fg_wh_factory extends Migration{
		/**
			* {@inheritdoc}
			*/
		public function safeUp(){
			$this->addColumn('factory', 'fg_warehouse_id',
			                 $this->integer(11)
			                      ->notNull()
			                      ->after('duns')
			);
			$this->addForeignKey(
				'fk_factory_fg_warehouse_id',
				'{{%factory}}', 'fg_warehouse_id',
				'{{%warehouse}}', 'id',
				'RESTRICT', 'RESTRICT'
			);
		}

		/**
			* {@inheritdoc}
			*/
		public function safeDown(){
			$this->dropForeignKey('fk_factory_fg_warehouse_id', '{{%factory}}');
			$this->dropColumn('factory', 'fg_warehouse_id');
		}
	}
