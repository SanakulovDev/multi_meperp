<?php

use yii\db\Migration;

class m200226_140000_add_type_column_to_part_part_wide_table extends Migration {
  public function safeUp() {
    $this->addColumn('{{%part_part_wide}}',
      'type',
      $this->string(1)->notNull()->after('id')
    );
  }
}
