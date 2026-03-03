<?php
namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "invoice_detail".
 *
 * @property int              $id
 * @property int              $part_order_id
 * @property int              $contract_id
 * @property int              $cont_inv_id
 * @property int              $part_id
 * @property string           $qty
 * @property string           $price
 * @property int              $err_sts Xatolik sababi
 * @property string           $remarks
 * @property int              $created_by
 * @property int              $created_at
 * @property int              $updated_by
 * @property int              $updated_at
 * @property ContainerInvoice $contInv
 * @property Contract         $contract
 * @property User             $createdBy
 * @property Part             $part
 * @property PartOrder        $partOrder
 * @property User             $updatedBy
 */
class InvoiceDetail extends ActiveRecord {

  /**
   * @inheritdoc
   */
  public static function tableName() {
    return 'invoice_detail';
  }

  /**
   * @inheritdoc
   */
  public function rules() {
    return [
      [['part_order_id', 'contract_id', 'cont_inv_id', 'part_id', 'err_sts', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
      [['part_order_id', 'contract_id'], 'required', 'on' => 'scenarioCreateOrUpdate'],
      [['cont_inv_id', 'part_id', 'qty', 'price', 'created_by', 'created_at'], 'required'],
      [['qty', 'price'], 'number'],
      [['remarks'], 'string', 'max' => 255],
      [['cont_inv_id'], 'exist', 'skipOnError' => true, 'targetClass' => ContainerInvoice::className(), 'targetAttribute' => ['cont_inv_id' => 'id']],
      [['contract_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contract::className(), 'targetAttribute' => ['contract_id' => 'id']],
      [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
      [['part_id'], 'exist', 'skipOnError' => true, 'targetClass' => Part::className(), 'targetAttribute' => ['part_id' => 'id']],
      [['part_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => PartOrder::className(), 'targetAttribute' => ['part_order_id' => 'id']],
      [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
    ];
  }

  /**
   * @inheritdoc
   */
  public function attributeLabels() {
    return [
      'id' => Yii::t('app', 'ID'),
      'cont_inv_id' => Yii::t('app', 'Container Invoices'),
      'part_order_id' => Yii::t('app', 'Part Order'),
      'part_id' => Yii::t('app', 'Part'),
      'contract_id' => Yii::t('app', 'Contract'),
      'qty' => Yii::t('app', 'Qty'),
      'price' => Yii::t('app', 'Price'),
      'err_sts' => Yii::t('app', 'Xatolik sababi'),
      'remarks' => Yii::t('app', 'Remarks'),
      'created_by' => Yii::t('app', 'Created by'),
      'created_at' => Yii::t('app', 'Created at'),
      'updated_by' => Yii::t('app', 'Updated by'),
      'updated_at' => Yii::t('app', 'Updated at'),
    ];
  }

  /**
   * @return ActiveQuery
   */
  public function getContInv() {
    return $this->hasOne(ContainerInvoice::className(), ['id' => 'cont_inv_id']);
  }

  public function getPart() {
    return $this->hasOne(Part::className(), ['id' => 'part_id']);
  }

  public function getContract() {
    return $this->hasOne(Contract::className(), ['id' => 'contract_id']);
  }

  public function getPartOrder() {
    return $this->hasOne(PartOrder::className(), ['id' => 'part_order_id']);
  }

  public function getInvoicePartProblems() {
    return $this->hasMany(InvoicePartProblem::className(), ['inv_detail_id' => 'id']);
  }

  public function getCreatedBy() {
    return $this->hasOne(User::className(), ['id' => 'created_by']);
  }

  public function getUpdatedBy() {
    return $this->hasOne(User::className(), ['id' => 'updated_by']);
  }

}
