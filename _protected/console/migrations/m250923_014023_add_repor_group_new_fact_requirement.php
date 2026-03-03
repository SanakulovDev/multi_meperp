<?php

use yii\db\Migration;

/**
 * Class m250923_014023_add_repor_group_new_fact_requirement
 */
class m250923_014023_add_repor_group_new_fact_requirement extends Migration
{
    public function safeUp()
    {
       
        Yii::$app->db->createCommand()->batchInsert('report', [ 'action', 'title', 'description', 'report_group_id', 'list_order', 'style'], [
            ['fact-requirement', 'Fact Requirement', 'Fact Requirement', 1, '1', 'fa fa-file-invoice'],
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
        // delete from `user_report` where `report_id`=(select id from `report` where `action`='calculate-product-index')
        $this->delete('user_report', ['report_id' => (new \yii\db\Query())->select('id')->from('report')->where(['action' => 'fact-requirement'])->scalar()]);
        // delete from `report` where `action`='calculate-product-index'
        $this->delete('report', ['action' => 'fact-requirement']);
    }
}
