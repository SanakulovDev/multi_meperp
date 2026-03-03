<?php

use yii\db\Migration;

/**
 * Class m230701_121914_add_monthly_report
 */
class m230701_121914_add_monthly_report extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //general
        $report_group_id = 4;
        $action = 'report-plan-month';
        Yii::$app->db->createCommand()->batchInsert('report', [ 'action', 'title', 'description', 'report_group_id', 'list_order', 'style'], [
            [$action, 'Plan -prodaj-month1', 'Plan -prodaj-month1', $report_group_id, '1', 'fa fa-file-production'],
        ])->execute();
        // insert user_report 
        $report_id = Yii::$app->db->createCommand('SELECT id FROM report WHERE action="report-plan-month"')->queryScalar();
        Yii::$app->db->createCommand()->batchInsert('user_report', ['user_id', 'report_id', 'created_at'], [
            [2, $report_id, date('Y-m-d H:i:s', time())],
        ])->execute();
    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230701_121914_add_monthly_report cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230701_121914_add_monthly_report cannot be reverted.\n";

        return false;
    }
    */
}
