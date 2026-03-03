<?php
use yii\db\Migration;

class m200818_101600_alter_value_column_on_fr_inv_det_cost_table extends Migration {

  public function safeUp() {

    $this->alterColumn(
      'freight_invoice_detail_cost', 'value',
      $this->decimal(20,2)->notNull()
    );
  }

  public function safeDown() {

  }

}

