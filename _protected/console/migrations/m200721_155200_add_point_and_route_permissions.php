<?php
use yii\db\Migration;

class m200721_155200_add_point_and_route_permissions extends Migration {

  public function Up() {
    
    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item`(`name`, `type`) 
				VALUES 
				('point-index',2),
				('point-create',2),
				('point-update',2),
				('point-delete',2),
				('point-download',2),

				('route-index',2),
				('route-create',2),
				('route-update',2),
				('route-delete',2),
				('route-download',2)"
    )->execute();

    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item_child`(`parent`, `child`) 
				VALUES 
				('admin','point-index'),
				('admin','point-create'),
				('admin','point-update'),
				('admin','point-delete'),
				('admin','point-download'),
				('admin','route-index'),
				('admin','route-create'),
				('admin','route-update'),
				('admin','route-delete'),
				('admin','route-download'),

				('superadmin','point-index'),
				('superadmin','point-create'),
				('superadmin','point-update'),
				('superadmin','point-delete'),
				('superadmin','point-download'),
				('superadmin','route-index'),
				('superadmin','route-create'),
				('superadmin','route-update'),
				('superadmin','route-delete'),
				('superadmin','route-download')"
    )->execute();
  }

}