<?php
use yii\db\Migration;

class m200819_185100_add_logx_permissions_to_role_logistics extends Migration {

  public function Up() {
    
    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item`(`name`, `type`) 
				VALUES('carrier-index',2),
				('carrier-create',2),
				('carrier-update',2),
				('carrier-delete',2),
				('carrier-xls',2),
	
				('air-shipment-index',2),
				('air-shipment-create',2),
				('air-shipment-update',2),
				('air-shipment-delete',2),
				('air-shipment-xls',2),
				
				('point-index',2),
				('point-create',2),
				('point-update',2),
				('point-delete',2),
				('point-download',2),

				('route-index',2),
				('route-create',2),
				('route-update',2),
				('route-delete',2),
				('route-download',2),

				('freight-invoice-index',2),
				('freight-invoice-view',2),
				('freight-invoice-create',2),
				('freight-invoice-update',2),
				('freight-invoice-delete',2),
				('freight-invoice-download',2), 

				('freight-invoice-detail-index',2),
				('freight-invoice-detail-view',2),
				('freight-invoice-detail-create',2),
				('freight-invoice-detail-update',2),
				('freight-invoice-detail-delete',2),
				('freight-invoice-detail-download',2)
				
				"
    )->execute();

    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item_child`(`parent`, `child`) 
				VALUES('logistics','carrier-index'),
				('logistics','carrier-create'),
				('logistics','carrier-update'),
				('logistics','carrier-delete'),
				('logistics','carrier-xls'),
				
				('logistics','air-shipment-index'),
				('logistics','air-shipment-create'),
				('logistics','air-shipment-update'),
				('logistics','air-shipment-delete'),
				('logistics','air-shipment-xls'),
				
				('logistics','point-index'),
				('logistics','point-create'),
				('logistics','point-update'),
				('logistics','point-delete'),
				('logistics','point-download'),

				('logistics','route-index'),
				('logistics','route-create'),
				('logistics','route-update'),
				('logistics','route-delete'),
				('logistics','route-download'),

				('logistics','freight-invoice-index'),
				('logistics','freight-invoice-view'),
				('logistics','freight-invoice-create'),
				('logistics','freight-invoice-update'),
				('logistics','freight-invoice-delete'),
				('logistics','freight-invoice-download'),

        ('logistics','freight-invoice-detail-index'),
				('logistics','freight-invoice-detail-view'),
				('logistics','freight-invoice-detail-create'),
				('logistics','freight-invoice-detail-update'),
				('logistics','freight-invoice-detail-delete'),
				('logistics','freight-invoice-detail-download'),

				('logistics','report-index')

			"
    )->execute();
  }

}