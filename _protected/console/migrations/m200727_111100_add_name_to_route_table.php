<?php
use yii\db\Migration;

class m200727_111100_add_name_to_route_table extends Migration {

  public function safeUp() {

    $this->addColumn(
      'route',
      'name',
      $this->string()->notNull()->after('to_point_id')
    );
  }

  public function safeDown() {
    $this->dropColumn('route', 'name');
  }

}

