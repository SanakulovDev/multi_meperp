<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "formulation".
 *
 * @property int $id
 * @property int $formulation_base_id
 * @property float|null $amount
 * @property int $customer_id
 * @property int|null $order_no
 * @property int $ulock
 * @property string|null $due_at
 * @property string|null $start_at
 * @property string|null $finish_at
 * @property float|null $act_rate
 * @property string|null $grind
 * @property string $packages
 *
 * @property Customer $customer
 * @property FormulationBase $formulationBase
 * @property Warehouse $ulock0
 * @property FormulationComponent[] $formulationComponents
 * @property FormulationSpecification[] $formulationSpecifications
 */
class Formulation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'formulation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['formulation_base_id', 'customer_id', 'ulock', 'packages'], 'required'],
            [['formulation_base_id', 'customer_id', 'order_no', 'ulock'], 'integer'],
            [['amount', 'act_rate'], 'number'],
            [['due_at', 'start_at', 'finish_at'], 'safe'],
            [['packages'], 'string'],
            [['grind'], 'string', 'max' => 50],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['customer_id' => 'id']],
            [['formulation_base_id'], 'exist', 'skipOnError' => true, 'targetClass' => FormulationBase::className(), 'targetAttribute' => ['formulation_base_id' => 'id']],
            [['ulock'], 'exist', 'skipOnError' => true, 'targetClass' => Warehouse::className(), 'targetAttribute' => ['ulock' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'formulation_base_id' => 'Formulation Base ID',
            'amount' => 'Amount',
            'customer_id' => 'Customer',
            'order_no' => 'Order No',
            'ulock' => 'Ulock',
            'due_at' => 'Due at',
            'start_at' => 'Start at',
            'finish_at' => 'Finish at',
            'act_rate' => 'Act Rate',
            'grind' => 'Grind',
            'packages' => 'Packages',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFormulationBase()
    {
        return $this->hasOne(FormulationBase::className(), ['id' => 'formulation_base_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUlock0()
    {
        return $this->hasOne(Warehouse::className(), ['id' => 'ulock']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFormulationComponents()
    {
        return $this->hasMany(FormulationComponent::className(), ['formulation_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFormulationSpecifications()
    {
        return $this->hasMany(FormulationSpecification::className(), ['formulation_id' => 'id']);
    }
}
