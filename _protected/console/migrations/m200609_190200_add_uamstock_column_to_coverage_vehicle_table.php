<?php
use app\models\Part;
use yii\db\Migration;

/**
 * Class m200420_081509_add_lock_column_to_airshipment
 */
class m200609_190200_add_uamstock_column_to_coverage_vehicle_table extends Migration {

  public function safeUp() {
    $this->addColumn(
      '{{%coverage_vehicle}}',
      'uamstock', $this->integer()->null()->after('stock')
    );
    $this->addColumn(
      '{{%coverage_vehicle_t}}',
      'uamstock', $this->integer()->null()->after('stock')
    );
  }

  public function safeDown() {
    $this->dropColumn('{{%coverage_vehicle}}', 'uamstock');
    $this->dropColumn('{{%coverage_vehicle_t}}', 'uamstock');
  }

}
