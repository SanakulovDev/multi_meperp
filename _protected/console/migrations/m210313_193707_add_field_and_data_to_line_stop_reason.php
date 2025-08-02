<?php
use yii\db\Migration;
use yii\helpers\Console;

/**
 * Class m210313_193707_add_field_and_data_to_line_stop_reason
 */
class m210313_193707_add_field_and_data_to_line_stop_reason extends Migration {

  /**
   * {@inheritdoc}
   */
  public function safeUp() {
      Yii::$app->db->createCommand('SET foreign_key_checks = 0')->execute();
      Yii::$app->db->createCommand()->truncateTable('line_stop_reason')->execute();
      $this->addColumn('line_stop_reason', 'type', $this->tinyInteger()->unsigned()->notNull()->after('id'));
      $this->dropColumn('line_stop', 'type');
      $list = [
        [0,'Танаффус', 'QC'],
        [0,'Колип/модел алмаштириш', 'QC'],
        [0,'Хом-ашё кайта юклаш', 'QC'],
        [0,'Режали таъмирлаш (ППР)', 'QC'],
        [0,'Укув машгулот йигинлари', 'QC'],
        [0,'Бошкалар', 'QC'],
        [1,'Дастгох бузилиши', 'QC'],
        [1,'Хом-ашё етишмовчилиги', 'QC'],
        [1,'Бутловчи кисм етишмовчилиги', 'QC'],
        [1,'Тара етишмовчилиги', 'QC'],
        [1,'Транспорт муаммоси (Кара)', 'QC'],
        [1,'Сифат муаммоси', 'QC'],
        [1,'Коммунал хизматлар муаммоси', 'QC'],
        [1,'Бошкалар', 'QC']
      ];
      Yii::$app->db->createCommand()
                   ->batchInsert('line_stop_reason', ['type', 'name', 'auth_item_name'], $list)->execute();
      Yii::$app->db->createCommand('SET foreign_key_checks = 1')->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown() {
      Yii::$app->db->createCommand('SET foreign_key_checks = 0')->execute();
      Yii::$app->db->createCommand()->truncateTable('line_stop_reason')->execute();
      $this->addColumn('line_stop', 'type', $this->tinyInteger()->unsigned()->notNull()->after('id'));
      $this->dropColumn('line_stop_reason', 'type');
      Yii::$app->db->createCommand('SET foreign_key_checks = 1')->execute();
  }

}
