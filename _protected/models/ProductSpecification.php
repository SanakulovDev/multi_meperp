<?php
namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "product_specification".
 *
 * @property int                        $id
 * @property string                     $code
 * @property int                        $part_id
 * @property string|null                $description
 * @property int                        $status 1-active; 0-inactive;
 * @property int|null                   $updated_by
 * @property int|null                   $updated_at
 * @property Part                       $part
 * @property ProductSpecificationItem[] $productSpecificationItems
 */
class ProductSpecification extends \yii\db\ActiveRecord {

  const STATUS_ACTIVE = 1;
  const STATUS_INACTIVE = 0;
  public $part_nm;

  public function statusList() {
    return [
      self::STATUS_ACTIVE => Yii::t('app', 'Актив'),
      self::STATUS_INACTIVE => Yii::t('app', 'Не актив'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function tableName() {
    return 'product_specification';
  }

  public function behaviors() {
    return [
      [
        'class' => TimestampBehavior::className(),
        'createdAtAttribute' => null,
      ],
      [
        'class' => BlameableBehavior::className(),
        'createdByAttribute' => null,
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['code', 'part_id'], 'required'],
      [['part_id', 'status', 'updated_by', 'updated_at'], 'integer'],
      [['code'], 'string', 'max' => 50],
      ['code', 'unique'],
      [['amount'], 'number'],
      ['amount', 'compare', 'compareValue' => 0, 'operator' => '>'],
      [['description'], 'string', 'max' => 191],
      [['part_id'], 'exist', 'skipOnError' => true, 'targetClass' => Part::className(), 'targetAttribute' => ['part_id' => 'id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels() {
    return [
      'id' => Yii::t('app', 'ID'),
      'code' => Yii::t('app', 'Specification code'),
      'amount' => Yii::t('app', 'Quantity'),
      'part_id' => Yii::t('app', 'Part number'),
      'part_nm' => Yii::t('app', 'Part name'),
      'description' => Yii::t('app', 'Specification remark'),
      'status' => Yii::t('app', 'Status'),
      'updated_by' => Yii::t('app', 'Updated by'),
      'updated_at' => Yii::t('app', 'Updated at'),
    ];
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getPart() {
    return $this->hasOne(Part::className(), ['id' => 'part_id']);
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getProductSpecificationItems() {
    return $this->hasMany(ProductSpecificationItem::className(), ['product_specification_id' => 'id']);
  }

  public function getStatusText(){	
    return $this->getStatusText()[$this->status];	
  }

  public function getProductionOrderCount() {
    return $this->hasMany(ProductionOrder::className(), ['product_specification_id' => 'id'])
                ->select(['product_specification_id', 'counted' => 'count(*)'])
                ->groupBy('product_specification_id');
  }

  public function getUpdatedBy() {
    return $this->hasOne(User::className(), ['id' => 'updated_by']);
  }

  public function getUpdatedAtFormatted() {
    return (!empty($this->updated_at)) ? date('d.m.Y H:i', $this->updated_at) : '';
  }

  public function copyToPartPart($sync = false) {
    if ($sync) {
      PartPart::deleteAll(['part_id' => $this->part_id]);
    }

    return Yii::$app->db->createCommand(
      "INSERT INTO `part_part`(`part_id`, `sub_part_id`, `usage_qty`, `warehouse_id`, `remark`, `status`, `created_by`, `created_at`, `updated_by`, `updated_at`)
					SELECT ps.part_id, psi.part_id as sub_part_id, psi.usage_qty / ps.amount as usage_qty, psi.warehouse_id, ps.description, ps.status, ps.updated_by, ps.updated_at, ps.updated_by, ps.updated_at
					FROM `product_specification` ps INNER JOIN `product_specification_item` psi ON ps.id=psi.product_specification_id
					WHERE ps.id = :id", [':id' => $this->id]
    )->execute();
  }

  public function afterSave($insert, $changedAttributes) {
    if ($this->status == self::STATUS_ACTIVE) {
      self::updateAll(['status' => self::STATUS_INACTIVE], [
        'AND',
        'part_id='.$this->part_id,
        ['<>', 'id', $this->id]
      ]);
    }
    parent::afterSave($insert, $changedAttributes);
  }

  public function getTree() {
    $query = "WITH RECURSIVE ancestors AS (
			SELECT
				psi.id,
				psi.part_id,
				psi.usage_qty,
				psi.warehouse_id,
				psi.product_specification_id,
				ps.code,
				ps.description,
				ps.status as ps_status,
				ps.amount as ps_amount,	
				IFNULL(psi.related_specification_id,0) as related_specification_id
			FROM product_specification_item psi
			LEFT JOIN product_specification ps ON psi.related_specification_id=ps.id 
			where psi.product_specification_id=:id
			UNION ALL
			SELECT
				psi.id,
				psi.part_id,
				psi.usage_qty,
				psi.warehouse_id,
				psi.product_specification_id,
				ps.code,
				ps.description,
				ps.status as ps_status,
				ps.amount as ps_amount,	
				IFNULL(psi.related_specification_id,0) as related_specification_id
			FROM product_specification_item psi
			INNER JOIN ancestors ON psi.product_specification_id=ancestors.related_specification_id
			LEFT JOIN product_specification ps ON psi.related_specification_id=ps.id 
			where ancestors.related_specification_id IS NOT NULL
			)
			SELECT 
			ancestors.product_specification_id,
			ancestors.part_id,
			p.part_no,
			p.part_name,
			p.part_color,
			p.state,
			u.unit_value,
			w.name as warehouse,
			ancestors.usage_qty,
			ancestors.code,
			ancestors.description,
			ancestors.ps_amount,
			ancestors.related_specification_id
			FROM ancestors
			INNER JOIN part p ON p.id=ancestors.part_id
			INNER JOIN unit u ON u.id=p.unit_id
			INNER JOIN warehouse w ON w.id=ancestors.warehouse_id";
    $rows = Yii::$app->db->createCommand($query, [':id' => $this->id])->queryAll();
    $tree = $this->createTree($rows, $this->id);

    return $tree;
  }

  private function createTree($list, $id) {
    $data = [];
    $model = [];
    foreach ($list as $item) {
      $psId = intval($item['product_specification_id']);
      $relatedPs = intval($item['related_specification_id']);
      if ($psId === $id) {
        $model['part_id'] = $item['part_id'];
        $model['part_no'] = $item['part_no'];
        $model['part_name'] = $item['part_name'];
        $model['part_color'] = $item['part_color'];
        $model['state'] = $item['state'];
        $model['unit_value'] = $item['unit_value'];
        $model['usage_qty'] = $item['usage_qty'];
        $model['warehouse'] = $item['warehouse'];
        $model['code'] = $item['code'];
        $model['description'] = $item['description'];
        $model['amount'] = $item['ps_amount'];
        if ($relatedPs !== 0) {
          $model['child'] = $this->createTree($list, $relatedPs);
        } else {
          unset($model['child']);
        }
        $data[] = $model;
      } else continue;
    }

    return $data;
  }

  public function getNextCode() {
    $query = "SELECT IFNULL(MAX(CAST( SUBSTRING_INDEX(code,'-',-1) AS UNSIGNED )),0)+1 as v
              FROM product_specification 
              WHERE part_id=:id";
    $v = Yii::$app->db->createCommand($query, [':id' => $this->part_id])->queryScalar();
    $firstPart = strrpos($this->code,'-');

    if(!$firstPart) $firstPart = $this->code; else $firstPart = substr($this->code,0,$firstPart);
    return $firstPart.'-'.str_pad($v, 5, "0", STR_PAD_LEFT);
  }

  public static function queryCreatefromBom($partIds = null) {
    if ($partIds) {
      $strIds = implode(',', $partIds);

      return "UPDATE product_specification SET status=0 WHERE status=1 AND part_id IN ($strIds);
              INSERT INTO `product_specification`(`code`, `part_id`, `description`, `status`, `updated_by`, `updated_at`) 
                SELECT concat(part.part_no,'-',min(part_part.id)) as code, 
                       part_id, part_part.remark,part_part.status,
                       IFNULL(part_part.updated_by, part_part.created_by) as updated_by, 
                       IFNULL(part_part.updated_at, part_part.created_at) as updated_at 
                FROM part_part INNER JOIN part on part.id=part_part.part_id
                WHERE part_part.part_id IN ($strIds)
                GROUP BY part_id;
              INSERT INTO `product_specification_item`(`product_specification_id`, `part_id`, `usage_qty`, `warehouse_id`)  
                SELECT ps.id, pp.sub_part_id, pp.usage_qty, pp.warehouse_id 
                FROM product_specification ps 
                INNER JOIN part_part pp ON ps.part_id = pp.part_id
                WHERE part_part.part_id IN ($strIds);  
					";
    } else {
      return "INSERT INTO `product_specification`(`code`, `part_id`, `description`, `status`, `updated_by`, `updated_at`) 
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
                WHERE pp.status=1;";
    }
  }

  public static function querySetCode() {
    return "UPDATE product_specification
            INNER JOIN ( SELECT 
                ps.id,
                p.part_no,
                part_id,
                p.part_name,
                ROW_NUMBER() OVER (PARTITION BY part_id ORDER BY ps.id) AS row_num
            FROM product_specification ps 
            INNER JOIN part p ON p.id=ps.part_id) T
              ON T.id = product_specification.id
            SET product_specification.code = CONCAT(T.part_no,'-',LPAD(T.row_num, 4, '0')), product_specification.description = T.part_name;";
  }

  public static function querySetOrderPs() {
    return "UPDATE production_order
            INNER JOIN product_specification ON production_order.part_id=product_specification.part_id AND product_specification.status = 1 
            SET production_order.product_specification_id = product_specification.id
						WHERE production_order.product_specification_id IS NULL;";
  }

  public static function querySetPsRelated() {
    return "UPDATE product_specification_item
							INNER JOIN product_specification ON product_specification_item.part_id=product_specification.part_id AND product_specification.status=1
							SET product_specification_item.related_specification_id = product_specification.id;";
  }
}
