<?php
use yii\db\Migration;

class m200409_161500_merge_seq_type_sequence_table extends Migration {

  public function safeUp() {
    /** 1 */
    $this->addColumn('{{%sequence}}', 'code', $this->string(20)->null()->defaultValue(null)->after('id'));
    $this->createIndex('uk_code', '{{%sequence}}', ['code'], true);
    $this->addColumn('{{%sequence}}', 'description', $this->string(100)->null()->defaultValue(null)->after('last_seq'));
    /** 2 */
    Yii::$app->db->createCommand(
      "
            UPDATE sequence seq SET 
              code = (SELECT name FROM sequence_type seq_type WHERE seq_type.id = seq.sequence_type_id),
              description = (SELECT description FROM sequence_type seq_type WHERE seq_type.id = seq.sequence_type_id);              
            ALTER TABLE `sequence` 
               CHANGE `code` `code` VARCHAR(20) NOT NULL, 
               CHANGE `description` `description` VARCHAR(100) NOT NULL; 
            INSERT IGNORE sequence(code, last_seq, description) VALUES('bomVersion', 0, 'BOM(part_part) last version');
            INSERT IGNORE sequence(code, last_seq, description) VALUES('supply', 0, 'label for supply');
          "
    )->execute();

    /** 3 */
    $this->dropForeignKey('fk_sequence_sequence_type_id', '{{%sequence}}');
    $this->dropIndex('{{%seq_type}}', 'sequence');
    $this->dropColumn('{{%sequence}}', 'sequence_type_id');
    $this->dropIndex('name', '{{%sequence_type}}');
    $this->dropTable('{{%sequence_type}}');
  }

  public function safeDown() {
    $this->dropColumn('{{%sequence}}', 'code');
    $this->dropColumn('{{%sequence}}', 'description');
  }

}
