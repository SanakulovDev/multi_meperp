<?php

use yii\db\Migration;

/**
 * Class m210601_112611_seed_part_mark_table
 */
class m210601_112611_seed_part_mark_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $query = Yii::$app->db->queryBuilder->batchInsert(
        'part_mark',
        ['name'],
        [
          ['name' => 'MEP 187'],
          ['name' => 'MEP 4201-11'],
          ['name' => 'MEP 4621-11'],
          ['name' => 'MEP 4233-21'],
          ['name' => 'MEP 4234-21'],
          ['name' => 'MEP 4232-21'],
          ['name' => 'MEP 4232-11'],
          ['name' => 'MEP 4236-21'],
          ['name' => 'MEP 4231-11'],
          ['name' => 'MEP 4231-21'],
          ['name' => 'MEP 4301-41'],
          ['name' => 'MEP PP-1352'],
          ['name' => 'MEP PP-T0572'],
          ['name' => 'MEP PPGF-2'],
          ['name' => 'MEP PPGF-2052'],
          ['name' => 'MEP PPGF-20'],
          ['name' => 'MEP PPGF-22'],
          ['name' => 'MEP PPGF-30'],
          ['name' => 'MEP PP-Z3774'],
          ['name' => 'MEP PPGF-1'],
          ['name' => 'MEP PPGF-3'],
          ['name' => 'MEP PPGF-4'],
          ['name' => 'MEP PP-Z4020G'],
          ['name' => 'MEP PP-TM3004'],
          ['name' => 'MEP PPGF-1505'],
          ['name' => 'MEP PPW-01'],
          ['name' => 'MEP PPW-02'],
          ['name' => 'MEP PPW-0125'],
          ['name' => 'MEP PP Z'],
          ['name' => 'MEP PP Z37'],
          ['name' => 'MEP PP Z38'],
          ['name' => 'MEP PP-M3617'],
          ['name' => 'MEP PP-Z 3022'],
          ['name' => 'MEP PP Z1'],
          ['name' => 'MEP PPGF-20M'],
          ['name' => 'MEP PPGF-2052CH'],
          ['name' => 'MEP PE- L2'],
          ['name' => 'MEP ABS M'],
          ['name' => 'MEP ABS M1'],
          ['name' => 'MEP ABS M2'],
          ['name' => 'MEP ABS M4'],
          ['name' => 'MEP ABS GF-10'],
          ['name' => 'MEP 4301-21'],
          ['name' => 'MEP 4301-11'],
          ['name' => 'MEP ABS -M'],
          ['name' => 'MEP PC/ABS -10'],
          ['name' => 'MEP PCGF -10'],
          ['name' => 'MEP SANGF-30'],
          ['name' => 'MEP SANGF-25'],
          ['name' => 'MEP SANGF-20'],
          ['name' => 'MEP PA66 GF-3'],
          ['name' => 'MEP PA66 GF-4'],
          ['name' => 'MEP PA66 M-40'],
          ['name' => 'MEP PA66 GF-30'],
          ['name' => 'MEP PA66'],
          ['name' => 'MEP PA66 Z'],
          ['name' => 'MEP PA6 GF-30'],
          ['name' => 'MEP PA6 GF-3'],
          ['name' => 'MEP PA6 GF-4'],
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
