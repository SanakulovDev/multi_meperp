<?php
use app\models\Defect;
use yii\db\Migration;

/**
 * Class m210326_122242_add_fields_to_line_stop_tables
 */
class m210326_122242_add_fields_to_line_stop_tables extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->addColumn("line_stop_reason", "fix_list", $this->text()->null());
    $this->addColumn("line_stop", "fix_list", $this->text()->null());
    $this->addColumn(
      "part_production_monitor",
      "start_time",
      $this->timestamp()
        ->null()
        ->after("broken_qty")
    );
    $this->addColumn(
      "part_production_monitor",
      "end_time",
      $this->timestamp()
        ->null()
        ->after("start_time")
    );
    $this->addColumn(
      "defect",
      "category",
      $this->tinyInteger()
        ->unsigned()
        ->defaultValue(Defect::CATEGORY_REWORK)
    );
    $tableOptions = "ENGINE=InnoDB";
    $this->createTable(
      "{{%production_defect}}",
      [
        "id" => $this->primaryKey(11),
        "part_production_monitor_id" => $this->integer()->notNull(),
        "defect_id" => $this->integer()->notNull(),
        "quantity" => $this->smallInteger()
          ->unsigned()
          ->notNull(),
        "created_by" => $this->integer(11)
          ->null()
          ->defaultValue(null),
        "created_at" => $this->integer(11)->notNull(),
        "updated_by" => $this->integer(11)
          ->null()
          ->defaultValue(null),
        "updated_at" => $this->integer(11)
          ->null()
          ->defaultValue(null),
      ],
      $tableOptions
    );
    $this->addForeignKey(
      "frk-production_defect-part_production_monitor_id",
      "production_defect",
      "part_production_monitor_id",
      "part_production_monitor",
      "id"
    );
    $this->addForeignKey("frk-production_defect-defect_id", "production_defect", "defect_id", "defect", "id");
    try {
      $this->dropIndex("code", "{{%defect}}");
    } catch (Exception $ex) {
      echo $ex->getMessage();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->dropForeignKey("frk-production_defect-defect_id", "production_defect");
    $this->dropForeignKey("frk-production_defect-part_production_monitor_id", "production_defect");
    $this->dropTable("{{%production_defect}}");
    $this->dropColumn("defect", "category");
    $this->dropColumn("part_production_monitor", "end_time");
    $this->dropColumn("part_production_monitor", "start_time");
    $this->dropColumn("line_stop", "fix_list");
    $this->dropColumn("line_stop_reason", "fix_list");
  }
}
