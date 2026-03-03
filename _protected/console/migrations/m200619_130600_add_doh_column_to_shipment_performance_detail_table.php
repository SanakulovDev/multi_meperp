<?php
use app\models\Part;
use yii\db\Migration;

/**
 * Class m200420_081509_add_lock_column_to_airshipment
 */
class m200619_130600_add_doh_column_to_shipment_performance_detail_table extends Migration {

  public function safeUp() {
    $this->addColumn(
      '{{%shipment_performance_detail}}',
      'doh', $this->integer()->null()->after('part_id')
    );
  }

  public function safeDown() {
    $this->dropColumn('{{%shipment_performance_detail}}', 'doh');
  }

}