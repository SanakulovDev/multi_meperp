<?php
use yii\db\Migration;

class m200423_152802_add_invoice_part_problem_index_permission extends Migration {

  public function Up() {
    $query = Yii::$app->db->queryBuilder->batchInsert(
      'auth_item',
      ["name", "type"],
      [
        ['invoice-part-problem-index', 2],
      ]
    );
    $query = str_replace('INSERT INTO', 'INSERT IGNORE', $query);
    Yii::$app->db->createCommand($query)->execute();
    $query = Yii::$app->db->queryBuilder->batchInsert(
      'auth_item_child',
      ["parent", "child"],
      [
        ['admin', 'invoice-part-problem-index'],
        ['superadmin', 'invoice-part-problem-index'],
        ['buyer', 'invoice-part-problem-index'],
        ['mfu', 'invoice-part-problem-index']
      ]
    );
    $query = str_replace('INSERT INTO', 'INSERT IGNORE', $query);
    Yii::$app->db->createCommand($query)->execute();
  }

}