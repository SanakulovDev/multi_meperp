<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%stock_info_wrapper}}".
 *
 * @property int $id
 * @property string|null $code
 * @property int|null $warehouse_id
 * @property int|null $type_id
 * @property int|null $give_user_id
 * @property string|null $comment
 * @property int|null $document_id
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Document $document
 * @property StockInfo[] $stockInfos
 */
class StockInfoWrapper extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%stock_info_wrapper}}';
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new \yii\db\Expression('NOW()'),
            ],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['warehouse_id', 'line', 'qty', 'part_id'], 'required'],
            [['warehouse_id', 'type_id', 'give_user_id', 'document_id', 'line', 'part_id'], 'integer'],
            [['created_at', 'updated_at', 'date'], 'safe'],
            [['qty'], 'number'],
            [['code', 'comment'], 'string', 'max' => 255],
            [['document_id'], 'exist', 'skipOnError' => true, 'targetClass' => Document::className(), 'targetAttribute' => ['document_id' => 'id']],
            [['part_id'], 'exist', 'skipOnError' => true, 'targetClass' => Part::className(), 'targetAttribute' => ['part_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'code' => Yii::t('app', 'Code'),
            'warehouse_id' => Yii::t('app', 'Warehouse'),
            'type_id' => Yii::t('app', 'Type ID'),
            'give_user_id' => Yii::t('app', 'User'),
            'comment' => Yii::t('app', 'Comment'),
            'document_id' => Yii::t('app', 'Document ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'part_id' => Yii::t('app', 'Product'),
            'qty' => Yii::t('app', 'Quantity'),
            'line' => Yii::t('app', 'Line'),
            'date'  => Yii::t('app', 'Date'),
        ];
    }

    /**
     * Gets query for [[Document]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocument()
    {
        return $this->hasOne(Document::className(), ['id' => 'document_id']);
    }
    
    public function getPart()
    {
        return $this->hasOne(Part::className(), ['id' => 'part_id']);
    }
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'give_user_id']);
    }

    public function getWarehouse() {
      return $this->hasOne(Warehouse::className(), ['id' => 'warehouse_id']);
    }

    /**
     * Gets query for [[StockInfos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStockInfos()
    {
        return $this->hasMany(StockInfo::className(), ['stock_info_wrapper_id' => 'id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
      if($insert){
        $this->code = sprintf("%06s", $this->id);
        $this->save();
      }
      return parent::afterSave($insert, $changedAttributes);
    }

    pubLic static function all()
    {
      return ArrayHelper::map(self::find()->all(), 'id', 'code');
    }
}
