<?php
use yii\db\Migration;

class m200725_130201_add_freight_invoice_permissions extends Migration {

  public function Up() {
    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item`(`name`, `type`) 
				VALUES 
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
				VALUES 
				('admin','freight-invoice-index'),
				('admin','freight-invoice-view'),
				('admin','freight-invoice-create'),
				('admin','freight-invoice-update'),
				('admin','freight-invoice-delete'),
				('admin','freight-invoice-download'),
        ('admin','freight-invoice-detail-index'),
				('admin','freight-invoice-detail-view'),
				('admin','freight-invoice-detail-create'),
				('admin','freight-invoice-detail-update'),
				('admin','freight-invoice-detail-delete'),
				('admin','freight-invoice-detail-download'),

				('superadmin','freight-invoice-index'),
				('superadmin','freight-invoice-view'),
				('superadmin','freight-invoice-create'),
				('superadmin','freight-invoice-update'),
				('superadmin','freight-invoice-delete'),
				('superadmin','freight-invoice-download'),
				('superadmin','freight-invoice-detail-index'),
				('superadmin','freight-invoice-detail-view'),
				('superadmin','freight-invoice-detail-create'),
				('superadmin','freight-invoice-detail-update'),
				('superadmin','freight-invoice-detail-delete'),
				('superadmin','freight-invoice-detail-download')"
    )->execute();
  }

  public function safeDown() {

  }

}
