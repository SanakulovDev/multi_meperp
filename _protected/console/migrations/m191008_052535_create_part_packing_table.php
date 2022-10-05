<?php
	use yii\db\Migration;

	/**
		* Handles the creation of table `{{%part_packing}}`.
		*/
	class m191008_052535_create_part_packing_table extends Migration{
		/**
			* {@inheritdoc}
			*/
		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable('{{%part_packing}}', [
				'id' => $this->primaryKey(11),
				'part_id' => $this->integer(11)->notNull(),
				'supplier_id' => $this->integer(11)->null()->defaultValue(null),

                'expandable' => $this->tinyInteger(1)->null()->defaultValue(0), // remove, add returnable
                'level1_pack_id' => $this->integer(11),

                'pack_qty' => $this->decimal(20, 5)->null()->defaultValue('1.00000'), // Standard pack
				'piece_weight' => $this->decimal(20, 5)->null()->defaultValue('1.00000'), // Part weight

                'net_weight' => $this->decimal(20, 5)->null()->defaultValue('1.00000'), // Standard pack * Part weight
				'gross_weight' => $this->decimal(20, 5)->null()->defaultValue('1.00000'), // Gross  weight


				'level2_pack_id' => $this->integer(11),
				'pack_pack_qty' => $this->integer(11)->unsigned(),
				'full_gross_weight' => $this->decimal(20, 5)->null()->defaultValue('1.00000'),

                'created_by' => $this->integer(11)->null()->defaultValue(null),
				'created_at' => $this->integer(11)->notNull(),
				'updated_by' => $this->integer(11)->null()->defaultValue(null),
				'updated_at' => $this->integer(11)->null()->defaultValue(null),
			], $tableOptions);
			$this->addForeignKey('fk_part_packing_part_id',
			                     '{{%part_packing}}', 'part_id',
			                     '{{%part}}', 'id',
			                     'CASCADE', 'CASCADE'
			);
			$this->addForeignKey('fk_part_packing_supplier_id',
			                     '{{%part_packing}}', 'supplier_id',
			                     '{{%supplier}}', 'id',
			                     'CASCADE', 'CASCADE'
			);
			$this->addForeignKey('fk_part_packing_created_by',
			                     '{{%part_packing}}', 'created_by',
			                     '{{%user}}', 'id',
			                     'SET NULL', 'SET NULL'
			);
			$this->addForeignKey('fk_part_packing_updated_by',
			                     '{{%part_packing}}', 'updated_by',
			                     '{{%user}}', 'id',
			                     'SET NULL', 'SET NULL'
			);
		}

		/**
			* {@inheritdoc}
			*/
		public function safeDown(){
			$this->dropForeignKey('fk_part_packing_supplier_id', '{{%part_packing}}');
			$this->dropForeignKey('fk_part_packing_part_id', '{{%part_packing}}');
			$this->dropForeignKey('fk_part_packing_created_by', '{{%part_packing}}');
			$this->dropForeignKey('fk_part_packing_updated_by', '{{%part_packing}}');
			$this->dropTable('{{%part_packing}}');
		}
	}
