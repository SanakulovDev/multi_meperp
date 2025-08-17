<?php
use yii\db\Migration;

class m201210_182300_add_moq_field_to_mfu_table extends Migration {

  public function safeUp() {

    $this->addColumn('mfu','moq', $this->integer()->after('consolidation_type_id'));

  }

  public function safeDown() {

    $this->dropColumn('mfu','moq');

  }

}

