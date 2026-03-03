<?php

use yii\db\Migration;

class m200224_174201_machine_mold_features extends Migration{

  public function init(){
    $this->db = 'db';
    parent::init();
  }

  public function safeUp(){
    $tableOptions = 'ENGINE=InnoDB';

    $this->createTable('{{%mold}}', [
      'id' => $this->primaryKey(10)->unsigned(),
      'mold_no' => $this->string(50)->notNull(),
      'production_date' => $this->date()->null()->defaultValue(null),
      'project_name' => $this->string(100)->null()->defaultValue('NULL'),
      'company_name' => $this->string(100)->null()->defaultValue('NULL'),
      'part_number' => $this->string(50)->notNull(),
      'part_name' => $this->string(100)->null()->defaultValue(null),
      'created_by' => $this->integer(11)->notNull(),
      'created_at' => $this->integer(11)->notNull(),
      'updated_by' => $this->integer(11)->null()->defaultValue(null),
      'updated_at' => $this->integer(11)->null()->defaultValue(null),
    ], $tableOptions);
    $this->addCommentOnTable('{{%mold}}', 'Оснастка(Qolip)');
    $this->createIndex('uk_mold_no', '{{%mold}}', ['mold_no'], true);

    $this->createTable('{{%machine}}', [
      'id' => $this->primaryKey(10)->unsigned(),
      'product_line_id' => $this->integer(11)->notNull()->comment('Зона'),
      'no' => $this->string(50)->notNull(),
      'title' => $this->string(100)->null()->defaultValue(null),
      'last_count' => $this->integer(10)->unsigned()->notNull()->defaultValue(0),
      'mold_id' => $this->integer(10)->unsigned()->null()->defaultValue(null),
      'sequence' => $this->integer(10)->unsigned()->notNull()->defaultValue(0),
      'status' => $this->tinyInteger(3)->unsigned()->notNull()->defaultValue(1),
      'created_by' => $this->integer(11)->notNull(),
      'created_at' => $this->integer(11)->notNull(),
      'updated_by' => $this->integer(11)->null()->defaultValue(null),
      'updated_at' => $this->integer(11)->null()->defaultValue(null),
    ], $tableOptions);
    $this->addCommentOnTable('{{%machine}}', 'Станок');
    $this->createIndex('uk_machine_zone_machine_mold', '{{%machine}}', ['product_line_id', 'no', 'mold_id'], true);
    $this->createIndex('uk_machine_zone_sequence', '{{%machine}}', ['product_line_id', 'sequence'], true);

    $this->createTable('{{%mold_machine}}', [
      'id' => $this->primaryKey(10)->unsigned(),
      'mold_id' => $this->integer(10)->unsigned()->notNull(),
      'machine_id' => $this->integer(10)->unsigned()->notNull(),
      'created_by' => $this->integer(11)->notNull(),
      'created_at' => $this->integer(11)->notNull(),
      'updated_by' => $this->integer(11)->null()->defaultValue(null),
      'updated_at' => $this->integer(11)->null()->defaultValue(null),
    ], $tableOptions);
    $this->addCommentOnTable('{{%mold_machine}}', 'Станок ва Оснастка(Qolip)');
    $this->createIndex('uk_mold_machine_mold_machine', '{{%mold_machine}}', ['mold_id', 'machine_id'], true);

    $this->createTable('{{%mold_part}}', [
      'id' => $this->primaryKey(10)->unsigned(),
      'mold_id' => $this->integer(10)->unsigned()->notNull(),
      'part_id' => $this->integer(11)->notNull(),
      'quantity' => $this->integer(11)->unsigned()->notNull()->defaultValue(1)->comment('Qancha chiqishi'),
      'created_by' => $this->integer(11)->notNull(),
      'created_at' => $this->integer(11)->notNull(),
      'updated_by' => $this->integer(11)->null()->defaultValue(null),
      'updated_at' => $this->integer(11)->null()->defaultValue(null),
    ], $tableOptions);
    $this->addCommentOnTable('{{%mold_part}}', 'Оснастка(Qolip) ва Детал');
    $this->createIndex('uk_mold_part_mold_id_part_id', '{{%mold_part}}', ['mold_id', 'part_id'], true);

    $this->addForeignKey(
      'fk_mold_created_by',
      '{{%mold}}', 'created_by',
      '{{%user}}', 'id',
      'RESTRICT', 'RESTRICT'
    );
    $this->addForeignKey(
      'fk_mold_updated_by',
      '{{%mold}}', 'updated_by',
      '{{%user}}', 'id',
      'RESTRICT', 'RESTRICT'
    );
    $this->addForeignKey(
      'fk_machine_created_by',
      '{{%machine}}', 'created_by',
      '{{%user}}', 'id',
      'RESTRICT', 'RESTRICT'
    );
    $this->addForeignKey(
      'fk_machine_mold_id',
      '{{%machine}}', 'mold_id',
      '{{%mold}}', 'id',
      'RESTRICT', 'RESTRICT'
    );
    $this->addForeignKey(
      'fk_machine_product_line_id',
      '{{%machine}}', 'product_line_id',
      '{{%product_line}}', 'id',
      'RESTRICT', 'RESTRICT'
    );
    $this->addForeignKey(
      'fk_machine_updated_by',
      '{{%machine}}', 'updated_by',
      '{{%user}}', 'id',
      'RESTRICT', 'RESTRICT'
    );
    $this->addForeignKey(
      'fk_mold_machine_created_by',
      '{{%mold_machine}}', 'created_by',
      '{{%user}}', 'id',
      'RESTRICT', 'RESTRICT'
    );
    $this->addForeignKey(
      'fk_mold_machine_mold_id',
      '{{%mold_machine}}', 'mold_id',
      '{{%mold}}', 'id',
      'RESTRICT', 'RESTRICT'
    );
    $this->addForeignKey(
      'fk_mold_machine_machine_id',
      '{{%mold_machine}}', 'machine_id',
      '{{%machine}}', 'id',
      'RESTRICT', 'RESTRICT'
    );
    $this->addForeignKey(
      'fk_mold_machine_updated_by',
      '{{%mold_machine}}', 'updated_by',
      '{{%user}}', 'id',
      'RESTRICT', 'RESTRICT'
    );

    $this->addForeignKey(
      'fk_mold_part_mold_id',
      '{{%mold_part}}', 'mold_id',
      '{{%mold}}', 'id',
      'RESTRICT', 'RESTRICT'
    );
    $this->addForeignKey(
      'fk_mold_part_part_id',
      '{{%mold_part}}', 'part_id',
      '{{%part}}', 'id',
      'RESTRICT', 'RESTRICT'
    );
    $this->addForeignKey(
      'fk_mold_part_created_by',
      '{{%mold_part}}', 'created_by',
      '{{%user}}', 'id',
      'RESTRICT', 'RESTRICT'
    );
    $this->addForeignKey(
      'fk_mold_part_updated_by',
      '{{%mold_part}}', 'updated_by',
      '{{%user}}', 'id',
      'RESTRICT', 'RESTRICT'
    );


  }

  public function safeDown(){
    $this->dropForeignKey('fk_mold_created_by', '{{%mold}}');
    $this->dropForeignKey('fk_mold_updated_by', '{{%mold}}');
    $this->dropForeignKey('fk_machine_created_by', '{{%machine}}');
    $this->dropForeignKey('fk_machine_mold_id', '{{%machine}}');
    $this->dropForeignKey('fk_machine_product_line_id', '{{%machine}}');
    $this->dropForeignKey('fk_machine_updated_by', '{{%machine}}');
    $this->dropForeignKey('fk_mold_machine_created_by', '{{%mold_machine}}');
    $this->dropForeignKey('fk_mold_machine_mold_id', '{{%mold_machine}}');
    $this->dropForeignKey('fk_mold_machine_machine_id', '{{%mold_machine}}');
    $this->dropForeignKey('fk_mold_machine_updated_by', '{{%mold_machine}}');
    $this->dropForeignKey('fk_mold_part_mold_id', '{{%mold_part}}');
    $this->dropForeignKey('fk_mold_part_part_id', '{{%mold_part}}');
    $this->dropForeignKey('fk_mold_part_created_by', '{{%mold_part}}');
    $this->dropForeignKey('fk_mold_part_updated_by', '{{%mold_part}}');

    $this->dropTable('{{%mold}}');
    $this->dropTable('{{%machine}}');
    $this->dropTable('{{%mold_machine}}');
    $this->dropTable('{{%mold_part}}');
  }
}
