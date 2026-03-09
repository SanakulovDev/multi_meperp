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
          ['name' => 'Neotherm187'],
          ['name' => 'Neotherm4201-11'],
          ['name' => 'Neotherm4621-11'],
          ['name' => 'Neotherm4233-21'],
          ['name' => 'Neotherm4234-21'],
          ['name' => 'Neotherm4232-21'],
          ['name' => 'Neotherm4232-11'],
          ['name' => 'Neotherm4236-21'],
          ['name' => 'Neotherm4231-11'],
          ['name' => 'Neotherm4231-21'],
          ['name' => 'Neotherm4301-41'],
          ['name' => 'NeothermPP-1352'],
          ['name' => 'NeothermPP-T0572'],
          ['name' => 'NeothermPPGF-2'],
          ['name' => 'NeothermPPGF-2052'],
          ['name' => 'NeothermPPGF-20'],
          ['name' => 'NeothermPPGF-22'],
          ['name' => 'NeothermPPGF-30'],
          ['name' => 'NeothermPP-Z3774'],
          ['name' => 'NeothermPPGF-1'],
          ['name' => 'NeothermPPGF-3'],
          ['name' => 'NeothermPPGF-4'],
          ['name' => 'NeothermPP-Z4020G'],
          ['name' => 'NeothermPP-TM3004'],
          ['name' => 'NeothermPPGF-1505'],
          ['name' => 'NeothermPPW-01'],
          ['name' => 'NeothermPPW-02'],
          ['name' => 'NeothermPPW-0125'],
          ['name' => 'NeothermPP Z'],
          ['name' => 'NeothermPP Z37'],
          ['name' => 'NeothermPP Z38'],
          ['name' => 'NeothermPP-M3617'],
          ['name' => 'NeothermPP-Z 3022'],
          ['name' => 'NeothermPP Z1'],
          ['name' => 'NeothermPPGF-20M'],
          ['name' => 'NeothermPPGF-2052CH'],
          ['name' => 'NeothermPE- L2'],
          ['name' => 'NeothermABS M'],
          ['name' => 'NeothermABS M1'],
          ['name' => 'NeothermABS M2'],
          ['name' => 'NeothermABS M4'],
          ['name' => 'NeothermABS GF-10'],
          ['name' => 'Neotherm4301-21'],
          ['name' => 'Neotherm4301-11'],
          ['name' => 'NeothermABS -M'],
          ['name' => 'NeothermPC/ABS -10'],
          ['name' => 'NeothermPCGF -10'],
          ['name' => 'NeothermSANGF-30'],
          ['name' => 'NeothermSANGF-25'],
          ['name' => 'NeothermSANGF-20'],
          ['name' => 'NeothermPA66 GF-3'],
          ['name' => 'NeothermPA66 GF-4'],
          ['name' => 'NeothermPA66 M-40'],
          ['name' => 'NeothermPA66 GF-30'],
          ['name' => 'NeothermPA66'],
          ['name' => 'NeothermPA66 Z'],
          ['name' => 'NeothermPA6 GF-30'],
          ['name' => 'NeothermPA6 GF-3'],
          ['name' => 'NeothermPA6 GF-4'],
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
