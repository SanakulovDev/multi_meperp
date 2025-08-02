<?php

use yii\db\Migration;

/**
 * Class m230715_101935_production_plan_fact_daily
 */
class m230715_101935_production_plan_fact_daily extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //general
        $report_group_id = 3;
        $action = 'production-plan-fact-daily';
        Yii::$app->db->createCommand()->batchInsert('report', [ 'action', 'title', 'description', 'report_group_id', 'list_order', 'style'], [
            [$action, 'Production Plan Fact Daily', 'Production plan fact daily', $report_group_id, '1', 'fa fa-file-production'],
        ])->execute();
        // insert user_report 
        $report_id = Yii::$app->db->createCommand('SELECT id FROM report WHERE action="production-plan-fact-daily"')->queryScalar();
        Yii::$app->db->createCommand()->batchInsert('user_report', ['user_id', 'report_id', 'created_at'], [
            [2, $report_id, date('Y-m-d H:i:s', time())],
        ])->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drop user_report

        $report_id = Yii::$app->db->createCommand('SELECT id FROM report WHERE action="production-plan-fact-daily"')->queryScalar();
        Yii::$app->db->createCommand()->delete('user_report', ['report_id' => $report_id])->execute();
        // drop report
        Yii::$app->db->createCommand()->delete('report', ['action' => 'production-plan-fact-daily'])->execute();
        
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230715_101935_production_plan_fact_daily cannot be reverted.\n";

        return false;
    }
    */
}
