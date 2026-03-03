<?php
use yii\db\Migration;

/**
 * Class m200603_071645_manage_ps_permissions
 */
class m200702_145000_mold_permissions extends Migration {

  /**
   * {@inheritdoc}
   */
  public function safeUp() {
    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item`(`name`, `type`, `description`, `created_at`, `updated_at`) 
              VALUES ('mold-part-delete', 2, 'mold-part-delete', 1580562767, 1580562767);
             INSERT IGNORE `auth_item_child`(`parent`, `child`) VALUES 
                ('superadmin','mold-part-delete'),
                ('admin','mold-part-delete'),
                ('pe','mold-part-delete'),
                ('mrpc','mold-part-delete'),
                ('counter','mold-part-delete')
                ;"
    )->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown() {
    echo "m200702_145000_mold_permissions cannot be reverted.\n";
    return false;
  }

}
