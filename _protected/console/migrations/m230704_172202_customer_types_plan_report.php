<?php

use yii\db\Migration;

/**
 * Class m230704_172202_customer_types_plan_report
 */
class m230704_172202_customer_types_plan_report extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         //general
         $report_group_id = 4;
         $action = 'customer-types-plan';
         Yii::$app->db->createCommand()->batchInsert('report', [ 'action', 'title', 'description', 'report_group_id', 'list_order', 'style'], [
             [$action, 'Customer types Plan', 'Customer types Plan', $report_group_id, '1', 'fa fa-file-production'],
         ])->execute();
         // insert user_report 
         $report_id = Yii::$app->db->createCommand('SELECT id FROM report WHERE action="customer-types-plan"')->queryScalar();
         Yii::$app->db->createCommand()->batchInsert('user_report', ['user_id', 'report_id', 'created_at'], [
             [2, $report_id, date('Y-m-d H:i:s', time())],
         ])->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230704_172202_customer_types_plan_report cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230704_172202_customer_types_plan_report cannot be reverted.\n";

        return false;
    }
    */
}
