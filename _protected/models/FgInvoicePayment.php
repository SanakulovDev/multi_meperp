<?php
namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Query;

/**
 * This is the model class for table "fg_invoice_payment".
 *
 * @property int    $id
 * @property string $no
 * @property string $date
 * @property int    $sales_contract_id
 * @property int    $currency_id
 * @property int    $waybill_id
 * @property float  $amount
 * @property int    $created_at
 * @property int    $created_by
 * @property int    $updated_at
 * @property int    $updated_by
 *
 * @property SalesContract $salesContract
 * @property Currency      $currency
 * @property Waybill       $waybill
 * @property User          $createdBy
 * @property User          $updatedBy
 */
class FgInvoicePayment extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'fg_invoice_payment';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            BlameableBehavior::className(),
        ];
    }

    public function beforeValidate()
    {
        if ($this->amount !== null && $this->amount !== '') {
            $this->amount = str_replace(' ', '', (string) $this->amount);
        }
        return parent::beforeValidate();
    }

    public function rules()
    {
        return [
            [['no', 'date', 'sales_contract_id', 'currency_id', 'amount'], 'required'],
            [['waybill_id'], 'default', 'value' => null],
            [['sales_contract_id', 'currency_id', 'waybill_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['amount'], 'number', 'min' => 0.0001],
            [['date'], 'safe'],
            [['no'], 'string', 'max' => 100],
            [['sales_contract_id'], 'exist', 'skipOnError' => true,
                'targetClass' => SalesContract::className(),
                'targetAttribute' => ['sales_contract_id' => 'id']],
            [['currency_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Currency::className(),
                'targetAttribute' => ['currency_id' => 'id']],
            [['waybill_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Waybill::className(),
                'targetAttribute' => ['waybill_id' => 'id']],
            [['waybill_id'], 'validateWaybillContract'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'                => Yii::t('app', 'ID'),
            'no'                => Yii::t('app', 'Receipt number'),
            'date'              => Yii::t('app', 'Date'),
            'sales_contract_id' => Yii::t('app', 'Sales contract'),
            'currency_id'       => Yii::t('app', 'Currency'),
            'waybill_id'        => Yii::t('app', 'Waybill (TTN)'),
            'amount'            => Yii::t('app', 'Amount'),
            'created_at'        => Yii::t('app', 'Created at'),
            'created_by'        => Yii::t('app', 'Created by'),
            'updated_at'        => Yii::t('app', 'Updated at'),
            'updated_by'        => Yii::t('app', 'Updated by'),
        ];
    }

    public function getSalesContract(): ActiveQuery
    {
        return $this->hasOne(SalesContract::className(), ['id' => 'sales_contract_id']);
    }

    public function getWaybill(): ActiveQuery
    {
        return $this->hasOne(Waybill::className(), ['id' => 'waybill_id']);
    }

    public function getCurrency(): ActiveQuery
    {
        return $this->hasOne(Currency::className(), ['id' => 'currency_id']);
    }

    public function getCreatedBy(): ActiveQuery
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    public function getUpdatedBy(): ActiveQuery
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    public function getCreatedAtFormatted(): string
    {
        return !empty($this->created_at) ? date('d.m.Y H:i', $this->created_at) : '';
    }

    public function getUpdatedAtFormatted(): string
    {
        return !empty($this->updated_at) ? date('d.m.Y H:i', $this->updated_at) : '';
    }

    /** Waybill number for display */
    public function getWaybillNo(): string
    {
        return $this->waybill ? $this->waybill->waybill_no : '';
    }

    public function validateWaybillContract($attribute): void
    {
        if (empty($this->waybill_id) || empty($this->sales_contract_id) || $this->hasErrors()) {
            return;
        }

        $contract = $this->salesContract;
        if ($contract === null) {
            return;
        }

        $exists = (new Query())
            ->from(['fiw' => 'fg_invoice_waybill'])
            ->innerJoin(['fi' => 'fg_invoice'], 'fi.id = fiw.fg_invoice_id')
            ->where([
                'fiw.waybill_id' => $this->waybill_id,
                'fi.contract' => $contract->contract_no,
                'fi.customer_id' => $contract->customer_id,
            ])
            ->exists();

        if (!$exists) {
            $this->addError($attribute, Yii::t('app', 'Selected waybill does not belong to the chosen contract.'));
        }
    }
}
