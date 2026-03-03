<?php
use yii\db\Migration;

class m200806_175000_alter_container_type_to_container_table extends Migration {

  public function safeUp() {

    $this->alterColumn(
      'container', 'container_type',
      $this->string(10)->null()
    );
  }

  public function safeDown() {

  }

}

