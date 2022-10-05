<?php

use yii\db\Migration;

class m200213_113500_add_is_label_column_to_production_order_table extends Migration {
  public function safeUp() {
    $this->addColumn('{{%production_order}}',
      'is_label',
      $this->integer()->notNull()->defaultValue(0)->after('is_printed')
    );
  }
}
