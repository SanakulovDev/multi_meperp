<?php

use yii\db\Migration;

class m200228_100500_add_priority_column_to_delivery_term_table extends Migration {
  public function safeUp() {
    $this->addColumn('{{%delivery_term}}',
      'priority',
      $this->tinyInteger(2)->after('name')
    );
  }
}
