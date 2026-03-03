<?php

namespace app\console\controllers;

use app\models\Part;
use app\models\PartPartWide;
use Yii;
use yii\console\Controller;

class BomController extends Controller
{

  private $raws = [];
  private $conses = [];
  private $semis = [];
  private $usageQty = 1;
  private $level = 0;

  private function recursiveRaw($part)
  {
    $this->level++;
    foreach ($part->activeComponents as $bom) {

      if (count($bom->subPart->activeComponents) > 0) {

        $this->usageQty = $this->usageQty * $bom->usage_qty;

        $this->recursiveRaw($bom->subPart);
      } else {
        
        $this->raws[] = [
          'raw_part_id' => $bom->sub_part_id,
          'usage_qty' => $this->usageQty * $bom->usage_qty,
          'warehouse_id' => $bom->warehouse_id,
          'level' => $this->level
        ];
      }
    }

    $this->level = 1;
    $this->usageQty = 1;
  }

  private function recursiveCons($part)
  {

    foreach ($part->activeComponents as $bom) {

      if (in_array($bom->subPart->contract_source_id, Yii::$app->params['consignment_contract_source_ids'])) {
        $this->conses[] = [
          'raw_part_id' => $bom->sub_part_id,
          'usage_qty' => $this->usageQty * $bom->usage_qty,
          'warehouse_id' => $bom->warehouse_id
        ];
      }

      if (count($bom->subPart->activeComponents) > 0) {

        $this->usageQty = $this->usageQty * $bom->usage_qty;

        $this->recursiveCons($bom->subPart);
      }
    }

    $this->usageQty = 1;
  }

  private function recursiveSemi($part)
  {

    foreach ($part->activeComponents as $bom) {

      if (in_array($bom->subPart->contract_source_id, Yii::$app->params['semi_contract_source_ids'])) {
        $this->semis[] = [
          'raw_part_id' => $bom->sub_part_id,
          'usage_qty' => $this->usageQty * $bom->usage_qty,
          'warehouse_id' => $bom->warehouse_id
        ];
      }

      if (count($bom->subPart->activeComponents) > 0) {

        $this->usageQty = $this->usageQty * $bom->usage_qty;

        $this->recursiveSemi($bom->subPart);
      }
    }

    $this->usageQty = 1;
  }

  public function actionIndex()
  {

    // Raw materials
    $start_bom = microtime(true);

    Yii::$app->db->createCommand('SET foreign_key_checks = 0')->execute();
    Yii::$app->db->createCommand()->truncateTable('part_part_wide')->execute();
    Yii::$app->db->createCommand('SET foreign_key_checks = 1')->execute();

    $parts = Part::find()->where([
      'status' => Part::STATUS_ACTIVE,
      'state' => [Part::STATE_SEMI, Part::STATE_FINISHED]
    ])->all();

    foreach ($parts as $part) {

      // if($part->part_no != '52056831') continue; 

      $this->usageQty = 1;
      $this->level = 0;
      $this->raws = [];
      $this->conses = [];
      $this->semis = [];

      $this->recursiveRaw($part);
      $this->recursiveCons($part);
      $this->recursiveSemi($part);

      foreach ($this->raws as $raw) {
        $partPartWide = new PartPartWide();
        $partPartWide->type = PartPartWide::TYPE_RAW;
        $partPartWide->part_id = $part->id;
        $partPartWide->level = $raw['level'];
        $partPartWide->sub_part_id = $raw['raw_part_id'];
        $partPartWide->usage_qty = $raw['usage_qty'];
        $partPartWide->warehouse_id = $raw['warehouse_id'];
        if (!$partPartWide->save()) {
        }
      }

      foreach ($this->conses as $cons) {
        $partPartWide = new PartPartWide();
        $partPartWide->type = PartPartWide::TYPE_CONSIGNMENT;
        $partPartWide->part_id = $part->id;
        $partPartWide->level = 0;
        $partPartWide->sub_part_id = $cons['raw_part_id'];
        $partPartWide->usage_qty = $cons['usage_qty'];
        $partPartWide->warehouse_id = $cons['warehouse_id'];
        if (!$partPartWide->save()) {
        }
      }

      foreach ($this->semis as $semi) {
        $partPartWide = new PartPartWide();
        $partPartWide->type = PartPartWide::TYPE_SEMI;
        $partPartWide->part_id = $part->id;
        $partPartWide->level = 0;
        $partPartWide->sub_part_id = $semi['raw_part_id'];
        $partPartWide->usage_qty = $semi['usage_qty'];
        $partPartWide->warehouse_id = $semi['warehouse_id'];
        if (!$partPartWide->save()) {
        }
      }

    }

    // ***********************

    $end_bom = microtime(true);
    $dur_bom = $end_bom - $start_bom;
    echo "\nBOM: " . round($dur_bom, 5) . " sec\n";
  }


  public function actionInitPs()
  {

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
					
					UPDATE product_specification
            INNER JOIN ( SELECT 
                ps.id,
                p.part_no,
                part_id,
                p.part_name,
                ROW_NUMBER() OVER (PARTITION BY part_id ORDER BY ps.id) AS row_num
            FROM product_specification ps 
            INNER JOIN part p ON p.id=ps.part_id) T
              ON T.id = product_specification.id
            SET product_specification.code = CONCAT(T.part_no,'-',LPAD(T.row_num, 4, '0')), product_specification.description = T.part_name;		
			"
    )->execute();
  }

  public function actionSetOrderPs()
  {
    Yii::$app->db->createCommand(
      "UPDATE production_order
            INNER JOIN product_specification ON production_order.part_id=product_specification.part_id AND product_specification.status = 1 
            SET production_order.product_specification_id = product_specification.id
						WHERE production_order.product_specification_id IS NULL;"
    )->execute();
  }

  public function actionSetPsRelated()
  {
    Yii::$app->db->createCommand(
      "UPDATE product_specification_item
							INNER JOIN product_specification ON product_specification_item.part_id=product_specification.part_id AND product_specification.status=1
							SET product_specification_item.related_specification_id = product_specification.id;"
    )->execute();
  }
}
