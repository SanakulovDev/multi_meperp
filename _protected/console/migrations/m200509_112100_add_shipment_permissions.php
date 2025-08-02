<?php
use yii\db\Migration;

class m200509_112100_add_shipment_permissions extends Migration {

  public function Up() {
    
    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item`(`name`, `type`) 
				VALUES 
				('shipment-index',2),
				('shipment-create',2),
				('shipment-delete',2),
        
				('shipment-detail-index',2),
				('shipment-detail-update',2),
				('shipment-detail-xls',2),
				('shipment-detail-recalculate',2)"
    )->execute();

    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item_child`(`parent`, `child`) 
				VALUES 
				('admin','shipment-index'),
				('admin','shipment-create'),
				('admin','shipment-delete'),
				('admin','shipment-detail-index'),
				('admin','shipment-detail-update'),
				('admin','shipment-detail-xls'),
				('admin','shipment-detail-recalculate'),

				('superadmin','shipment-index'),
				('superadmin','shipment-create'),
				('superadmin','shipment-delete'),
				('superadmin','shipment-detail-index'),
				('superadmin','shipment-detail-update'),
				('superadmin','shipment-detail-xls'),
				('superadmin','shipment-detail-recalculate')"
    )->execute();
  }

}