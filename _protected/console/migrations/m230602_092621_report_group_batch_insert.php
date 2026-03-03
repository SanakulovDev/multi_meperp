<?php

use yii\db\Migration;

/**
 * Class m230602_092621_report_group_batch_insert
 */
class m230602_092621_report_group_batch_insert extends Migration
{
    public function safeUp()
    {
        Yii::$app->db->createCommand()->batchInsert('report_group', ['id', 'name', 'order', 'icon', 'color'], [
            [8, 'Accounting', '8', 'fa fa-file-invoice', '#795548'],
        ])->execute();
        //insert report table 
        Yii::$app->db->createCommand()->batchInsert('report', [ 'action', 'title', 'description', 'report_group_id', 'list_order', 'style'], [
            ['calculate', 'Calculator', 'Calculator product', 8, '1', 'fa fa-file-invoice'],
        ])->execute();
        // insert user_report 
        $report_id = Yii::$app->db->createCommand('SELECT id FROM report WHERE action="calculate"')->queryScalar();
        Yii::$app->db->createCommand()->batchInsert('user_report', ['user_id', 'report_id', 'created_at'], [
            [2, $report_id, date('Y-m-d H:i:s', time())],
        ])->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // delete from `report_group` where `id`=8
        $this->delete('report_group', ['id' => 8]);
        // delete from `user_report` where `report_id`=(select id from `report` where `action`='calculate-product-index')
        $this->delete('user_report', ['report_id' => (new \yii\db\Query())->select('id')->from('report')->where(['action' => 'calculate'])->scalar()]);
        // delete from `report` where `action`='calculate-product-index'
        $this->delete('report', ['action' => 'calculate']);
    }

    
}
