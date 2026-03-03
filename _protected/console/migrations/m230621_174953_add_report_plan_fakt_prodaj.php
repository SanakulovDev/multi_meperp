<?php

use yii\db\Migration;

/**
 * Class m230621_174953_add_report_plan_fakt_prodaj
 */
class m230621_174953_add_report_plan_fakt_prodaj extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //general
        $report_group_id = 4;
        $action = 'plan-prodaj';
        Yii::$app->db->createCommand()->batchInsert('report', [ 'action', 'title', 'description', 'report_group_id', 'list_order', 'style'], [
            [$action, 'Plan -Fakt Prodaj', 'Plan -Fakt Prodaj', $report_group_id, '1', 'fa fa-file-production'],
        ])->execute();
        // insert user_report 
        $report_id = Yii::$app->db->createCommand('SELECT id FROM report WHERE action="plan-prodaj"')->queryScalar();
        Yii::$app->db->createCommand()->batchInsert('user_report', ['user_id', 'report_id', 'created_at'], [
            [2, $report_id, date('Y-m-d H:i:s', time())],
        ])->execute();


        // month


        $report_group_id = 4;
        $action = 'plan-prodaj-month';
        Yii::$app->db->createCommand()->batchInsert('report', [ 'action', 'title', 'description', 'report_group_id', 'list_order', 'style'], [
            [$action, 'Plan -prodaj-month', 'Plan -prodaj-month', $report_group_id, '1', 'fa fa-file-production'],
        ])->execute();
        
        // insert user_report 
        $report_id = Yii::$app->db->createCommand('SELECT id FROM report WHERE action="plan-prodaj-month"')->queryScalar();
        Yii::$app->db->createCommand()->batchInsert('user_report', ['user_id', 'report_id', 'created_at'], [
            [2, $report_id, date('Y-m-d H:i:s', time())],
        ])->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230621_174953_add_report_plan_fakt_prodaj cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230621_174953_add_report_plan_fakt_prodaj cannot be reverted.\n";

        return false;
    }
    */
}
