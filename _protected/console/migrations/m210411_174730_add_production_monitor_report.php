<?php
use app\models\Report;
use app\models\ReportGroup;
use yii\db\Migration;

/**
 * Class m210411_174730_add_production_monitor_report
 */
class m210411_174730_add_production_monitor_report extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $group = ReportGroup::find()->with('reports')->where(['name' => 'Production'])->one();
      if ($group) {

        $orders = [];
        foreach ($group->reports as $report) $orders[] = $report->list_order;

        $report = new Report();
        $report->report_group_id = $group->id;
        $report->action = 'production-monitor';
        $report->title = 'Production results';
        $report->description = 'Production results';
        $report->list_order = max($orders) + 1;
        $report->style = 'ion ion-pie-graph:red';
        $report->save();
      }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Report::find()->where(['action' => 'production-monitor'])->one()->delete();
    }
}
