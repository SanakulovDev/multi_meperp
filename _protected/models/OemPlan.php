<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "oem_plan".
 *
 * @property int $id
 * @property int $model_id
 * @property string $target_date
 * @property int $quantity
 */
class OemPlan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oem_plan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['model_id', 'target_date', 'quantity'], 'required'],
            [['model_id', 'quantity'], 'integer'],
            [['model_id', 'target_date'], 'unique', 'targetAttribute' => ['model_id', 'target_date']],
            [['target_date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'model_id' => Yii::t('app', 'Product model'),
            'target_date' => Yii::t('app', 'Date'),
            'quantity' => Yii::t('app', 'Quantity'),
        ];
    }

    public function getModel(){
        return $this->hasOne(ProductModel::className(), ['id' => 'model_id']);
    }
}
