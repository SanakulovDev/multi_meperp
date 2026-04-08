<?php

use yii\db\Migration;

/**
 * Adds sales-debt-status report to the Sales report group (id=4)
 * and grants access to all users who have access to report 33 (sales-payment-status).
 */
class m260406_000002_add_sales_debt_status_report extends Migration
{
    public function safeUp()
    {
        // Insert report record (idempotent via INSERT IGNORE)
        $this->execute("
            INSERT IGNORE INTO report (action, title, description, list_order, report_group_id)
            VALUES ('sales-debt-status', 'Debt status by customers', 'Shipment vs payment balance per customer', 34, 4)
        ");

        // Grant access to the same users who have access to report 33
        $this->execute("
            INSERT IGNORE INTO user_report (user_id, report_id, created_at)
            SELECT ur.user_id, (SELECT id FROM report WHERE action = 'sales-debt-status'), NOW()
            FROM user_report ur
            WHERE ur.report_id = 33
        ");

        // RBAC permission (idempotent)
        $this->execute("INSERT IGNORE INTO auth_item (name, type) VALUES ('report-sales-debt-status', 2)");
        $this->execute("
            INSERT IGNORE INTO auth_item_child (parent, child) VALUES
            ('admin',      'report-sales-debt-status'),
            ('superadmin', 'report-sales-debt-status')
        ");

        Yii::$app->getAuthManager()->invalidateCache();
    }

    public function safeDown()
    {
        $reportId = Yii::$app->db->createCommand(
            "SELECT id FROM report WHERE action = 'sales-debt-status'"
        )->queryScalar();

        if ($reportId) {
            $this->execute("DELETE FROM user_report WHERE report_id = $reportId");
            $this->execute("DELETE FROM report WHERE id = $reportId");
        }

        $this->execute("DELETE FROM auth_item_child WHERE child = 'report-sales-debt-status'");
        $this->execute("DELETE FROM auth_item WHERE name = 'report-sales-debt-status'");

        Yii::$app->getAuthManager()->invalidateCache();
    }
}
