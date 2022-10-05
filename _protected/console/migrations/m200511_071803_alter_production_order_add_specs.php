<?php

use yii\db\Migration;

/**
 * Class m200511_071803_alter_production_order_add_specs
 */
class m200511_071803_alter_production_order_add_specs extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%production_order}}', 'product_specification_id', $this->integer(11)->null());
        $this->addColumn('{{%product_specification}}', 'amount', $this->decimal(25, 10)->notNull()->defaultValue('1.00000'));
        $this->addColumn('{{%product_specification_item}}', 'related_specification_id', $this->integer(11)->null());
        // migration part-part to product-specification
        Yii::$app->db->createCommand(
            "UPDATE `product_specification` SET `status`= 0;
             INSERT INTO `product_specification`(`code`, `part_id`, `description`, `status`, `updated_by`, `updated_at`) 
                SELECT concat(part.part_no,'-',min(part_part.id)) as code, 
                       part_id, part_part.remark,part_part.status,
                       IFNULL(part_part.updated_by, part_part.created_by) as updated_by, 
                       IFNULL(part_part.updated_at, part_part.created_at) as updated_at 
                FROM part_part INNER JOIN part on part.id=part_part.part_id
                WHERE part_part.status=1
                GROUP BY part_id;

                INSERT INTO `product_specification_item`(`product_specification_id`, `part_id`, `usage_qty`, `warehouse_id`)  
                    SELECT ps.id, pp.sub_part_id, pp.usage_qty, pp.warehouse_id 
                    FROM product_specification ps 
                    INNER JOIN part_part pp ON ps.part_id = pp.part_id
                    WHERE pp.status=1;
                UPDATE production_order
                    INNER JOIN product_specification ON production_order.part_id=product_specification.part_id
                    SET production_order.product_specification_id = product_specification.id;
                UPDATE product_specification_item
                    INNER JOIN product_specification ON product_specification_item.part_id=product_specification.part_id AND product_specification.status=1
                    SET product_specification_item.related_specification_id = product_specification.id;    
            "
          )->execute();

        Yii::$app->db->createCommand(
			"INSERT IGNORE `auth_item`(`name`, `type`) 
								VALUES 
								('product-specification-duplicate',2);
			 INSERT IGNORE `auth_item_child`(`parent`, `child`) 
								VALUES 
								('pe', 'product-specification-duplicate'),
								('superadmin', 'product-specification-duplicate'),
								('admin', 'product-specification-duplicate');
			"
		)->execute();
		Yii::$app->authManager->invalidateCache(); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand(
            "DELETE FROM `product_specification_item`;
             TRUNCATE TABLE `product_specification_item`;
             DELETE FROM `product_specification`;
             TRUNCATE TABLE `product_specification`;
            "
          )->execute();

          $this->dropColumn('{{%production_order}}', 'product_specification_id');
          $this->dropColumn('{{%product_specification}}', 'amount');
          $this->dropColumn('{{%product_specification_item}}', 'related_specification_id');
    }
}
