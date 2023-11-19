<?php
namespace app\models;

use phpDocumentor\Reflection\Types\Expression;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "fg_invoice".
 *
 * @property int                $id
 * @property int                $factory_id
 * @property string             $invoice_no          Increment by factory and year(invoice_date)
 * @property string             $invoice_date
 * @property int                $customer_id
 * @property string             $contract            Sales contract of Customer
 * @property string             $contract_date
 * @property string             $rec_person_fullname Doverennost FIO
 * @property string             $rec_person_regno    Doverennost RegNo
 * @property string             $driver
 * @property string             $truck
 * @property string             $manager
 * @property string             $account
 * @property string             $sender
 * @property int                $vat                 QQS % xisobida
 * @property int                $excise              Аксиз налог % xisobida
 * @property string             $comment
 * @property int                $confirmed_at
 * @property int                $confirmed_by
 * @property int                $created_at
 * @property int                $created_by
 * @property int                $updated_at
 * @property int                $updated_by
 * @property User               $confirmedBy
 * @property User               $createdBy
 * @property Customer           $customer
 * @property Factory            $factory
 * @property User               $updatedBy
 * @property FgInvoiceDetail[]  $fgInvoiceDetails
 * @property FgInvoiceWaybill[] $fgInvoiceWaybills
 */
class FgInvoice extends ActiveRecord {

  /**
   * {@inheritdoc}
   */
  public static function tableName() {
    return 'fg_invoice';
  }

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['factory_id', 'contract', 'invoice_no', 'invoice_date', 'customer_id', 'created_at', 'created_by'], 'required'],
      [['factory_id', 'customer_id', 'vat', 'excise', 'confirmed_at', 'confirmed_by', 'created_at', 'created_by', 'updated_at', 'updated_by'],
        'integer',
        'message' => Yii::t('app', 'Must be an integer...')
      ],
      [['invoice_date', 'contract_date'], 'safe'],
      [['invoice_no', 'rec_person_fullname', 'rec_person_regno', 'driver', 'manager', 'account', 'sender'], 'string', 'max' => 100],
      [['contract'], 'string', 'max' => 255],
      [['truck'], 'string', 'max' => 30],
      [['comment'], 'string', 'max' => 255],
      [['factory_id', 'invoice_no', 'invoice_date', 'customer_id'], 'unique', 'targetAttribute' => ['factory_id', 'invoice_no', 'invoice_date', 'customer_id']],
      [['confirmed_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['confirmed_by' => 'id']],
      [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
      [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['customer_id' => 'id']],
      [['factory_id'], 'exist', 'skipOnError' => true, 'targetClass' => Factory::className(), 'targetAttribute' => ['factory_id' => 'id']],
      [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
//				['factory_id', 'customer_id', 'confirmed_by', 'created_by', 'message'=>Yii::t('app', '{nameAttribute} must be an integer..')],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels() {
    return [
      'id' => Yii::t('app', 'ID'),
      'factory_id' => Yii::t('app', 'Factory'),
      'invoice_no' => Yii::t('app', 'Invoice no'),
      'invoice_date' => Yii::t('app', 'Invoice Date'),
      'customer_id' => Yii::t('app', 'Customer'),
      'contract' => Yii::t('app', 'Sales contract'),
      'contract_date' => Yii::t('app', 'Sales contract date'),
      'rec_person_fullname' => Yii::t('app', 'Doverennost FIO'),
      'rec_person_regno' => Yii::t('app', 'Doverennost RegNo'),
      'driver' => Yii::t('app', 'Driver'),
      'truck' => Yii::t('app', 'Truck'),
      'manager' => Yii::t('app', 'Manager'),
      'account' => Yii::t('app', 'Account'),
      'sender' => Yii::t('app', 'Sender'),
      'vat' => Yii::t('app', 'QQS % xisobida'),
      'excise' => Yii::t('app', 'Аксиз налог % xisobida'),
      'comment' => Yii::t('app', 'Comment'),
      'created_at' => Yii::t('app', 'Created at'),
      'created_by' => Yii::t('app', 'Created by'),
      'confirmed_at' => Yii::t('app', 'Confirmed at'),
      'confirmed_by' => Yii::t('app', 'Confirmed by'),
      'updated_at' => Yii::t('app', 'Updated at'),
      'updated_by' => Yii::t('app', 'Updated by'),
    ];
  }

  public function getCustomer() {
    return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
  }

  public function getSalesContract() {
    return $this->hasOne(SalesContract::className(), ['contract_no' => 'contract']);
  }

  public function getCustomerSalesContract() {
    return $this->hasOne(SalesContract::className(), ['contract_no' => 'contract'])
                ->andOnCondition(['customer_id'=> $this->customer_id]);
  }

  public function getFactory() {
    return $this->hasOne(Factory::className(), ['id' => 'factory_id']);
  }

  public function getFgInvoiceDetails() {
    return $this->hasMany(FgInvoiceDetail::className(), ['fg_invoice_id' => 'id']);
  }
  
  public function getFgInvoiceDetails2() {
    return $this->hasOne(FgInvoiceDetail::className(), ['fg_invoice_id' => 'id']);
  }

  public function getFgInvoiceWaybills() {
    return $this->hasMany(FgInvoiceWaybill::className(), ['fg_invoice_id' => 'id']);
  }

  public function getFgInvoiceReceipts() {
    return $this->hasMany(FgInvoiceReceipt::className(), ['fg_invoice_id' => 'id']);
  }

  public function getConfirmedBy() {
    return $this->hasOne(User::className(), ['id' => 'confirmed_by']);
  }

  public function getCreatedBy() {
    return $this->hasOne(User::className(), ['id' => 'created_by']);
  }

  public function getCreatedAtFormatted() {
    return (!empty($this->created_at)) ? date('d.m.Y H:i', $this->created_at) : '';
  }

  public function getUpdatedBy() {
    return $this->hasOne(User::className(), ['id' => 'updated_by']);
  }

  public function getUpdatedAtFormatted() {
    return (!empty($this->updated_at)) ? date('d.m.Y H:i', $this->updated_at) : '';
  }

  public function getStatusName() {
    return ($this->confirmed_by == null) ? Yii::t('app', 'Pending') : Yii::t('app', 'Confirmed');
  }

  public function fields() {
    return [
      'id',
      'invoice_no',
      'invoice_date',
      'contract',
      'contract_date',
      'rec_person_fullname',
      'rec_person_regno',
      'driver',
      'truck',
      'manager',
      'account',
      'sender',
      'vat',
      'excise',
      'comment',
      'factory' => function() {
        return $this->isRelationPopulated('factory') ? $this->factory : ['id' => $this->factory_id];
      },
      'customer' => function() {
        return $this->isRelationPopulated('customer') ? $this->customer : ['id' => $this->customer_id];
      },
      'items' => function() {
        return $this->isRelationPopulated('fgInvoiceDetails') ? $this->fgInvoiceDetails : [];
      },
      'createdBy' => function() {
        return $this->isRelationPopulated('createdBy') ? $this->createdBy : ['id' => $this->created_by];
      },
      'created_at' => function() {
        return $this->getCreatedAtFormatted();
      },
      'updatedBy' => function() {
        return $this->isRelationPopulated('updatedBy') ? $this->updatedBy : ['id' => $this->updated_by];
      },
      'updated_at' => function() {
        return $this->getUpdatedAtFormatted();
      },
    ];
  }

}
