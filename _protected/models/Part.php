<?php
namespace app\models;

use app\console\controllers\CoverageController;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "part".
 *
 * @property int $id
 * @property int $state 0-raw material, 1-semi, 2-FG
 * @property string $part_no
 * @property string|null $part_name
 * @property string|null $part_color
 * @property int|null $unit_id
 * @property int|null $part_type_id
 * @property int|null $contract_source_id
 * @property string|null $pack_size
 * @property float|null $coefficient
 * @property int|null $warehouse_id
 * @property string|null $remark
 * @property int $status
 * @property int $created_by
 * @property int $created_at
 * @property int|null $updated_by
 * @property int|null $updated_at
 * @property string|null $comment
 * @property int|null $commented_by
 * @property string|null $commented_at
 * @property int|null $actual_contract_detail_id
 *
 * @property ContractDetail $actualContractDetail
 * @property ApiDetail[] $apiDetails
 * @property Api[] $apis
 * @property Balance[] $balances
 * @property User $commentedBy
 * @property ContractDetail[] $contractDetails
 * @property ContractSource $contractSource
 * @property User $createdBy
 * @property Crushing[] $crushings
 * @property DocumentDetailSub[] $documentDetailSubs
 * @property DocumentDetailSub[] $documentDetailSubs0
 * @property DocumentDetail[] $documentDetails
 * @property FormulationBase[] $formulationBases
 * @property FormulationComponent[] $formulationComponents
 * @property InvoiceDetail[] $invoiceDetails
 * @property Lms[] $lms
 * @property Mfu $mfu
 * @property MoldPart[] $moldParts
 * @property Mold[] $molds
 * @property Pack[] $packs
 * @property PartActiveLog[] $partActiveLogs
 * @property PartOrderDetail[] $partOrderDetails
 * @property PartOrder[] $partOrders
 * @property PartPacking[] $partPackings
 * @property PartPartWide[] $partPartWides
 * @property PartPartWide[] $partPartWides0
 * @property PartPart[] $partParts
 * @property PartPart[] $partParts0
 * @property Part[] $parts
 * @property ProductGroup $productGroup
 * @property ProductParts[] $productParts
 * @property ProductSpecificationItem[] $productSpecificationItems
 * @property ProductSpecification[] $productSpecifications
 * @property ProductionOrderSub[] $productionOrderSubs
 * @property ProductionOrder[] $productionOrders
 * @property ProductionPlanSub[] $productionPlanSubs
 * @property ProductionPlan[] $productionPlans
 * @property Product[] $products
 * @property Req[] $reqs
 * @property SalesContractDetail[] $salesContractDetails
 * @property ShipmentDetail[] $shipmentDetails
 * @property Stock[] $stocks
 * @property Part[] $subParts
 * @property Unit $unit
 * @property User $updatedBy
 * @property Warehouse $warehouse
 * @property Warehouse[] $warehouses
 */
class Part extends ActiveRecord
{

  // the list of status values that can be stored in user table
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const STATE_RAW = 0;
    const STATE_SEMI = 1;
    const STATE_FINISHED = 2;
    const STATE_CASTLE = 3;

    const SCENARIO_VPUTI = 'v-puti';
    /**
     * List of names for each status.
     *
     * @var array
     */
    public $statusList = [
    self::STATUS_ACTIVE => 'Актив',
    self::STATUS_INACTIVE => 'Не актив',
  ];

    public $stateList = [
        self::STATE_RAW => 'Базовое сырье',
        self::STATE_SEMI => 'Полуфабрикат',
        self::STATE_FINISHED => 'Готовый продукт',
        self::STATE_CASTLE => 'Замеc'
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'part';
    }

    public function fields()
    {
        return [
      'id',
      'part_no',
      'part_name',
      'part_color',
      'pack_size',
      'state' => function () {
          return $this->getStateText();
      },
      'unit' => function () {
          return $this->isRelationPopulated('unit') ? $this->unit->unit_value : "";
      },
      'partType' => function () {
          return $this->isRelationPopulated('partType') ? $this->partType->typename : "";
      },
    ];
    }

    public static function findPart($part_no)
    {
        return self::find()->where(['part_no' => $part_no])->one();
    }

    public static function getPartNumbers()
    {
        return ArrayHelper::map(self::find()->where(['status' => Part::STATUS_ACTIVE])->all(), 'id', 'part_no');
    }

    public static function getAllPartNumbers()
    {
        return ArrayHelper::map(self::find()->all(), 'id', 'part_no');
    }

    public static function findOneByPartNumber($part_numner)
    {
        return self::find()->where(['part_no' => $part_numner])->one();
    }

    /**
     * Returns a list of behaviors that this component should behave as.
     *
     * @return array
     */
    public function behaviors()
    {
        return [
      TimestampBehavior::className(),
    ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
      [['state', 'part_no', 'status', 'created_by', 'created_at', 'unit_id', 'part_name'], 'required'],
      ['warehouse_id', 'required', 'when' => function ($model) {
          return $model->state != self::STATE_RAW;
      }, 'whenClient' => "function (attribute, value) {
                    return $('#state_id').val() != ".self::STATE_RAW.";
                }"
      ],
      [['state', 'part_type_id', 'contract_source_id', 'warehouse_id', 'status', 'created_by', 'created_at', 'updated_by', 'updated_at', 'commented_by', 'actual_contract_detail_id'], 'integer'],
      [['coefficient'], 'number'],
      [['comment'], 'string'],
      [['arrived_qty'], 'number'],
      [['commented_at', 'part_color', 'arrived_at'], 'safe'],
      [['arrived_qty', 'arrived_at'], 'required', 'on'=>self::SCENARIO_VPUTI],
      [['part_no'], 'string', 'max' => 50],
      [['part_name', 'part_color', 'pack_size', 'remark'], 'string', 'max' => 255],
      [
        'part_no',
        'match',
        //					'pattern' => '/^[A-Za-z0-9_-()]+$/u',
        'pattern' => '/[^а-яА-ЯёЁ:@#]+$/u',
        'message' => Yii::t('app', '{attribute} cannot contain cyrillic letters and (:@#) symbols')
      ],
      [['part_no'], 'unique'],
      [['commented_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['commented_by' => 'id']],
      [['contract_source_id'], 'exist', 'skipOnError' => true, 'targetClass' => ContractSource::className(), 'targetAttribute' => ['contract_source_id' => 'id']],
      [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
      [['unit_id'], 'exist', 'skipOnError' => true, 'targetClass' => Unit::className(), 'targetAttribute' => ['unit_id' => 'id']],
      [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
      [['warehouse_id'], 'exist', 'skipOnError' => true, 'targetClass' => Warehouse::className(), 'targetAttribute' => ['warehouse_id' => 'id']],
      [['actual_contract_detail_id'], 'exist', 'skipOnError' => true, 'targetClass' => ContractDetail::className(), 'targetAttribute' => ['actual_contract_detail_id' => 'id']],
    ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'part_no' => Yii::t('app', 'Part No'),
            'part_name' => Yii::t('app', 'Part name'),
            'part_color' => Yii::t('app', 'Part color'),
            'unit_id' => Yii::t('app', 'Unit'),
            'part_type_id' => Yii::t('app', 'Part type'),
            'product_group_id' => Yii::t('app', 'Product group'),
            'contract_source_id' => Yii::t('app', 'Contract source'),
            'warehouse_id' => Yii::t('app', 'Floc'),
            'fg_warehouse_id' => Yii::t('app', 'FG storage'),
            'remark' => Yii::t('app', 'Remark'),
            'pack_size' => Yii::t('app', 'Pack size'),
            'coefficient' => Yii::t('app', 'Coefficient'),
            'status' => Yii::t('app', 'Status'),
            'state' => Yii::t('app', 'State'),
            'created_by' => Yii::t('app', 'Created by'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_by' => Yii::t('app', 'Updated by'),
            'updated_at' => Yii::t('app', 'Updated at'),
            'comment' => Yii::t('app', 'Comment'),
            'commented_by' => Yii::t('app', 'Commented by'),
            'commented_at' => Yii::t('app', 'Commented at'),
            'actual_contract_detail_id' => Yii::t('app', 'Contract detail'),
            'arrived_qty' => Yii::t('app', 'Next Arrival'),
            'arrived_at' => Yii::t('app', 'Date')
        ];
    }

    public function getActualContractDetail()
    {
        return $this->hasOne(ContractDetail::className(), ['id' => 'actual_contract_detail_id']);
    }

    public function getUnit()
    {
        return $this->hasOne(Unit::className(), ['id' => 'unit_id']);
    }

    public function getMfu()
    {
        return $this->hasOne(Mfu::className(), ['part_id' => 'id']);
    }

    public function getCommentedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'commented_by']);
    }

    public function getPartType()
    {
        return $this->hasOne(PartType::className(), ['id' => 'part_type_id']);
    }

    public function getWarehouse()
    {
        return $this->hasOne(Warehouse::className(), ['id' => 'warehouse_id']);
    }

    public function getContractSource()
    {
        return $this->hasOne(ContractSource::className(), ['id' => 'contract_source_id']);
    }

    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    public function getDocumentDetails()
    {
        return $this->hasMany(DocumentDetail::className(), ['part_id' => 'id']);
    }

    public function getDocuments()
    {
        return $this->hasMany(Document::className(), ['id' => 'document_id'])->viaTable('document_detail', ['part_id' => 'id']);
    }

    public function getContractDetails()
    {
        return $this->hasMany(ContractDetail::className(), ['part_id' => 'id']);
    }

    public function getStocks()
    {
        return $this->hasMany(Stock::className(), ['part_id' => 'id']);
    }

    public function getWarehouses()
    {
        return $this->hasMany(Warehouse::className(), ['id' => 'warehouse_id'])->viaTable('stock', ['part_id' => 'id']);
    }

    public function getPartinfo()
    {
        return $this->part_no.' '.$this->part_name.' '.$this->part_color.' '.$this->remark;
    }

    public function getUpdatedAtFormatted()
    {
        return (!empty($this->updated_at)) ? date('d.m.Y H:i', $this->updated_at) : '';
    }

    public function getCreatedAtFormatted()
    {
        return (!empty($this->created_at)) ? date('d.m.Y H:i', $this->created_at) : '';
    }

    public function getCommentedAtFormatted()
    {
        return (!empty($this->commented_at)) ? date('d.m.Y H:i', strtotime($this->commented_at)) : '-';
    }

    public function getStatusText()
    {
        return $this->statusList[$this->status];
    }

    public function getStateText()
    {
        return Yii::t('app', $this->stateList[$this->state]);
    }

    public function getSubParts()
    {
        return $this->hasMany(PartPart::className(), ['part_id' => 'id']);
    }

    public function getHasSubParts()
    {
        return count($this->subParts) > 0;
    }

    public function getProductionPlans()
    {
        return $this->hasMany(ProductionPlan::className(), ['part_id' => 'id']);
    }

    public function getComponents()
    {
        return $this->hasMany(PartPart::className(), ['part_id' => 'id']);
    }

    public function getActiveComponents()
    {
        return PartPart::find()->where(['status' => PartPart::STATUS_ACTIVE, 'part_id' => $this->id])->all();
    }

    public function getPartWithPackSize()
    {
        return $this->part_no.' '.$this->part_color.'  Pack size: '.$this->pack_size;
    }


    public function getUloc()
    {
        if ($this->state == self::STATE_FINISHED) {
            return $this->fg_warehouse_id;
        }

        return PartPart::find()->where(['sub_part_id' => $this->id, 'status' => 1])->one()->warehouse_id;
    }

    public function getBom()
    {
        return PartPart::find()->where(['sub_part_id' => $this->id, 'status' => 1])->one();
    }

    public function getContractDetail()
    {
        return ContractDetail::find()->where(['part_id' => $this->id])->one();
    }

    public function getPartPackings()
    {
        return $this->hasMany(PartPacking::className(), ['part_id' => 'id']);
    }

    public function getActualContract()
    {
        return $this->actualContractDetail;
        //return $this->findActualContract($cs_ids);
    }

    public function findActualContract($cs_ids = null)
    {
        // Ushbu detal uchun asosiy narx belgilangan yoki yo'qligini tekshirish
        $actualContractDetail = ContractDetail::find()
                                          ->where([
                                            'and',
                                            ['part_id' => $this->id],
                                            ['is_primary_price' => 1]
                                          ])->one();

        // Agar detal uchun asosiy narx belgilangan bolsa o'shani aks holda eski usul boyicha olingan natijani qaytaramiz
        return ($actualContractDetail) ? $actualContractDetail : $this->getActualContractOld($cs_ids);
    }

    public function getActualContractOld($cs_ids = null)
    {
        // Kontrakt amal qilish muddati tugamagan bo'lishi kk
        // Kontrakt aktiv holatda bo'lishi kk
        // Delivery term priority bo'yicha oladi
        // Agar yuqoridagi shartlar bo'yicha 1 dan ortiq narx chiqsa to'g'ri kelgan birini olamiz
        $priceQuery = ContractDetail::find()
                                ->joinWith('contract')
                                ->where([
                                  'and',
                                  ['part_id' => $this->id],
                                  ['contract.status' => Contract::STATUS_ACTIVE],
                                  ['<=', 'contract.contract_date', date('Y-m-d')],
                                  ['>=', 'contract.expiry_date', date('Y-m-d')]
                                ]);
        if ($cs_ids) {
            $priceQuery->andWhere(['contract.contract_source_id' => $cs_ids]);
        }
        $deliveryTerms = DeliveryTerm::find()->orderBy(['priority' => SORT_ASC])->all();
        foreach ($deliveryTerms as $dTerm):
      $priceQueryClone = clone $priceQuery;
        $contractDetail = $priceQueryClone->andWhere(['delivery_term_id' => $dTerm->id])->one();
        if ($contractDetail) {
            return $contractDetail;
        }
        endforeach;

        return null;
    }

    public function getAverageUsage()
    {
        $query = "
    
      select count(*) cnt, sum(qty), (sum(qty) / count(*)) avg_usage from production_plan_sub pps 
      where 
        part_id = :part_id and 
        plandate between curdate() and curdate() + INTERVAL 3 MONTH and
        qty <> 0

    ";
        $data = Yii::$app->db->createCommand($query, [':part_id' => $this->id])->queryOne();

        return $data['avg_usage'] ?? 0;
    }

    /**
     * Gets query for [[PartOrderDetails]].
     *
     * @return ActiveQuery
     */
    public function getPartOrderDetails()
    {
        return $this->hasMany(PartOrderDetail::className(), ['part_id' => 'id']);
    }

    /**
     * Gets query for [[PartOrders]].
     *
     * @return ActiveQuery
     */
    public function getPartOrders()
    {
        return $this->hasMany(PartOrder::className(), ['id' => 'part_order_id'])->viaTable('part_order_detail', ['part_id' => 'id']);
    }

    public function getAllComponents()
    {
        return $this->hasMany(PartPartWide::className(), ['part_id' => 'id']);
    }

    public function getPartNameSpecial()
    {
        return preg_replace('/[^A-Za-z0-9\-]/', ' ', $this->part_name);
    }

    public function getPartActiveLogs()
    {
        return $this->hasMany(PartActiveLog::className(), ['part_no' => 'part_no']);
    }

    public function getCoverage()
    {
        return $this->hasMany(Req::class, ['part_id' => 'id'])
            ->where(['type' => CoverageController::TYPE_STOCK]);
    }

    /**
     * kg ni metrga aylantiradi: metr = kg / (coefficient / 1000)
     */
    public function kgToMeter($kg)
    {
        if (!empty($this->coefficient) && $this->coefficient > 0) {
            return $kg / ($this->coefficient / 1000);
        }
        return null;
    }

    // get part name
    public static function getPartName($part_id)
    {
        $part = self::findOne($part_id);
        if ($part) {
            return $part->part_name;
        }

        return '';
    }

}
