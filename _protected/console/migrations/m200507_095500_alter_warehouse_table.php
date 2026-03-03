<?php

use app\models\PaymentControl;
use yii\db\Migration;


class m200507_095500_alter_warehouse_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand(
            "ALTER TABLE warehouse DROP FOREIGN KEY IF EXISTS `frk-warehouse-warehouse_report_group_id`"
          )->execute();

          ;
      

       // $this->dropForeignKey('frk-warehouse-warehouse_report_group_id','{{%warehouse}}');

        $this->addForeignKey('frk-warehouse-warehouse_report_group_id',
                                'warehouse',
                                'warehouse_report_group_id',
                                'warehouse_report_group',
                                'id');

        
    }

    
}
