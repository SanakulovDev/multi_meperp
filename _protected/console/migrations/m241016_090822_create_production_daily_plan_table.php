<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%production_daily_plan}}`.
 */
class m241016_090822_create_production_daily_plan_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%production_daily_plan}}', [
            'id' => $this->primaryKey(),
            'part_id'   =>  $this->integer(),
            'production_date' =>    $this->date(),
            'warehouse_id'  =>  $this->integer(),
            'shift' =>  $this->integer(),
            'target_qty'    =>  $this->integer(),
            'line'  =>  $this->integer(),
            'type'  =>  $this->integer(),
            'remark'    =>  $this->string(255)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%production_daily_plan}}');
    }
}
