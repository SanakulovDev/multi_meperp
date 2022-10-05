<?php
use yii\db\Migration;

class m200507_164818_drop_columns_visitor_table extends Migration {

  public function safeUp() {
    $this->dropColumn('{{%visitor}}', 'user_agent');
    $this->dropColumn('{{%visitor}}', 'user_browser');
    $this->dropColumn('{{%visitor}}', 'user_browser_version');
    $this->dropColumn('{{%visitor}}', 'user_platform');
    $this->dropColumn('{{%visitor}}', 'user_device_type');
  }

}
