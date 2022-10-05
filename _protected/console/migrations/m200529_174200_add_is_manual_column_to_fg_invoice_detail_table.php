<?php
use yii\db\Migration;

/**
 * Class m200420_081509_add_lock_column_to_airshipment
 */
class m200529_174200_add_is_manual_column_to_fg_invoice_detail_table extends Migration {

  public function safeUp() {
    $this->addColumn(
      '{{%fg_invoice_detail}}',
      'source',
      $this->boolean()->notNull()
           ->defaultValue(1)
           ->comment('1-FG WH; 0-From production line;')
           ->after('unit_id')
    );
  }

  public function safeDown() {
    $this->dropColumn('{{%fg_invoice_detail}}', 'source');
  }

}
