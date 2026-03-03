<?php
use yii\db\Migration;

class m210119_121600_add_chief_accountant_field_to_factory_table extends Migration {

  public function safeUp() {

    $this->addColumn('factory','chief_accountant', $this->string()->null());

  }

  public function safeDown() {

    $this->dropColumn('factory','chief_accountant');

  }

}

