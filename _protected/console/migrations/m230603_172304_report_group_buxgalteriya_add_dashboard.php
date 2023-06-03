<?php

use yii\db\Migration;

/**
 * Class m230603_172304_report_group_buxgalteriya_add_dashboard
 */
class m230603_172304_report_group_buxgalteriya_add_dashboard extends Migration
{
    /**
     * {@inheritdoc}
     * otchotdagi headerdaagi buxgalteriya qismiga dashboard/index qo'shildi
     */
    public function safeUp()
    {
        
        //insert report table 
        $report_group_id = 8;
        Yii::$app->db->createCommand()->batchInsert('report', [ 'action', 'title', 'description', 'report_group_id', 'list_order', 'style'], [
            ['material-report', 'Material Report', 'Material Report', $report_group_id, '1', 'fa fa-file-invoice'],
        ])->execute();
        // insert user_report 
        $report_id = Yii::$app->db->createCommand('SELECT id FROM report WHERE action="material-report"')->queryScalar();
        Yii::$app->db->createCommand()->batchInsert('user_report', ['user_id', 'report_id', 'created_at'], [
            [2, $report_id, date('Y-m-d H:i:s', time())],
        ])->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $action = 'material-report';
        $id = 9;
        // delete from `user_report` where `report_id`=(select id from `report` where `action`='material-report')
        $this->delete('user_report', ['report_id' => (new \yii\db\Query())->select('id')->from('report')->where(['action' => $action])->scalar()]);
        // delete from `report` where `action`='calculate-product-index'
        $this->delete('report', ['action' => $action]);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230603_172304_report_group_buxgalteriya_add_dashboard cannot be reverted.\n";

        return false;
    }
    */
}
