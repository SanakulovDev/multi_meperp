<?php

use yii\db\Migration;

/**
 * Class m210526_135456_seed_part_color_table
 */
class m210526_135456_seed_part_color_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $query = Yii::$app->db->queryBuilder->batchInsert(
        'part_color',
        ['name'],
        [
          ['name'=>'00 NP'],
          ['name'=>'01 Black'],
          ['name'=>'02 Blue'],
          ['name'=>'03 Dark grey'],
          ['name'=>'04 Light grey'],
          ['name'=>'05 Damas Grey'],
          ['name'=>'06 Lacetti Grey'],
          ['name'=>'07 Silver'],
          ['name'=>'08 White'],
          ['name'=>'09 Ivore'],
          ['name'=>'10 Red'],
          ['name'=>'11 Grey'],
          ['name'=>'12 White Ivore'],
          ['name'=>'13 White Blue'],
          ['name'=>'14 White Blue'],
          ['name'=>'15 Urban'],
          ['name'=>'16 White Ivore'],
          ['name'=>'17 White Ivore'],
          ['name'=>'18 Blue'],
          ['name'=>'19 Charco'],
          ['name'=>'20 Light Titanium'],
          ['name'=>'21 Green'],
          ['name'=>'22 Yellow']
        ]
      );
      $query = str_replace('INSERT INTO', 'INSERT IGNORE', $query);
      $result = Yii::$app->db->createCommand($query)->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }
}
