<?php

use yii\db\Migration;

/**
 * Class m230819_092530_add_monthly_production_plan_report
 */
class m230819_092530_add_monthly_production_plan_report extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //general
        $report_group_id = 1;
        $title = 'Monthly Requirement Short';
        $action = 'monthly-requirement-short';
        Yii::$app->db->createCommand()->batchInsert('report', [ 'action', 'title', 'description', 'report_group_id', 'list_order', 'style'], [
            [$action, $title, $title, $report_group_id, '1', 'fa fa-file-production'],
        ])->execute();
        // insert user_report 
        $report_id = Yii::$app->db->createCommand('SELECT id FROM report WHERE action="monthly-requirement-short"')->queryScalar();
        Yii::$app->db->createCommand()->batchInsert('user_report', ['user_id', 'report_id', 'created_at'], [
            [2, $report_id, date('Y-m-d H:i:s', time())],
        ])->execute();

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        //delete  user_report 
        $report_id = Yii::$app->db->createCommand('SELECT id FROM report WHERE action="monthly-requirement-short"')->queryScalar();
        Yii::$app->db->createCommand()->delete('user_report', ['report_id' => $report_id])->execute();
        //delete report
        Yii::$app->db->createCommand()->delete('report', ['action' => 'monthly-requirement-short'])->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230819_092530_add_monthly_production_plan_report cannot be reverted.\n";

        return false;
    }
    */
}
