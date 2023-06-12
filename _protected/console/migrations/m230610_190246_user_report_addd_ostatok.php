<?php

use yii\db\Migration;

/**
 * Class m230610_190246_user_report_addd_ostatok
 */
class m230610_190246_user_report_addd_ostatok extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // prodajaga ostatka gp (stock/dashboard qo'shildi)
        $report_group_id = 4;
        Yii::$app->db->createCommand()->batchInsert('report', [ 'action', 'title', 'description', 'report_group_id', 'list_order', 'style'], [
            ['stock-dashboard', 'Remaining GP', 'Remaining GP', $report_group_id, '1', 'fa fa-file-dashboard'],
        ])->execute();
        // insert user_report 
        $report_id = Yii::$app->db->createCommand('SELECT id FROM report WHERE action="stock-dashboard"')->queryScalar();
        Yii::$app->db->createCommand()->batchInsert('user_report', ['user_id', 'report_id', 'created_at'], [
            [2, $report_id, date('Y-m-d H:i:s', time())],
        ])->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $action = 'stock-dashboard';
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
        echo "m230610_190246_user_report_addd_ostatok cannot be reverted.\n";

        return false;
    }
    */
}
