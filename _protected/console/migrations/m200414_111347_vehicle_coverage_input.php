<?php
use yii\db\Migration;

class m200414_111347_vehicle_coverage_input extends Migration {

  public function init() {
    $this->db = 'db';
    parent::init();
  }

  public function safeUp() {
    $tableOptions = 'ENGINE=InnoDB';
    $this->createTable('{{%vehicle_coverage_input}}', [
      'id' => $this->primaryKey(10)->unsigned(),
      'model_id' => $this->integer(11)->notNull(),
      'quantity' => $this->decimal(25, 10)->unsigned()->Null()->defaultValue(0),
      'for_date' => $this->date()->null()->defaultValue(null),
      'description' => $this->integer(1)->unsigned()->notNull()->comment('1-Current stock; 2-Paid not shipped order volume; 3-Intransit ETA;'),
      'created_at' => $this->integer(11)->notNull(),
      'created_by' => $this->integer(11)->notNull(),
    ], $tableOptions);
    $this->addCommentOnTable('{{%vehicle_coverage_input}}', 'Vehicle set coverage input(Инфо по машинакомплектом)');

    $this->createIndex('fk_vehicle_coverage_input_created_by','{{%vehicle_coverage_input}}',['created_by'],false);
    $this->createIndex('fk_vehicle_coverage_input_model_id','{{%vehicle_coverage_input}}',['model_id'],false);

    $this->addForeignKey(
      'fk_vehicle_coverage_input_created_by',
      '{{%vehicle_coverage_input}}', 'created_by',
      '{{%user}}', 'id',
      'RESTRICT', 'RESTRICT'
    );
    $this->addForeignKey(
      'fk_vehicle_coverage_input_model_id',
      '{{%vehicle_coverage_input}}', 'model_id',
      '{{%product_model}}', 'id',
      'RESTRICT', 'RESTRICT'
    );
    /** add permission */
    Yii::$app->db->createCommand(
      "      
         INSERT IGNORE auth_item(name, type) 
         VALUES ('vehicle-coverage-input-index',  2), 
                ('vehicle-coverage-input-refresh', 2);                
         INSERT IGNORE auth_item_child(parent, child) 
         VALUES ('superadmin', 'vehicle-coverage-input-index'),
                ('superadmin', 'vehicle-coverage-input-refresh');
          "
    )->execute();
  }

  public function safeDown() {
    $this->dropForeignKey('fk_vehicle_coverage_input_created_by', '{{%vehicle_coverage_input}}');
    $this->dropForeignKey('fk_vehicle_coverage_input_model_id', '{{%vehicle_coverage_input}}');
    $this->dropIndex('fk_vehicle_coverage_input_created_by', '{{%vehicle_coverage_input}}');
    $this->dropIndex('fk_vehicle_coverage_input_model_id', '{{%vehicle_coverage_input}}');

    $this->dropTable('{{%vehicle_coverage_input}}');
  }

}
