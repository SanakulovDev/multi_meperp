<?php

use app\models\Lms;
use yii\db\Migration;

/**
 * Class m200302_102116_alter_lms_add_bms
 */
class m200302_102116_alter_lms_add_bms extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $this->addColumn('{{%lms}}', 'bms', $this->tinyInteger(1)->defaultValue(Lms::SIZE_SMALL)->after('dloc'));
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->dropColumn('{{%lms}}', 'bms');
  }
}
