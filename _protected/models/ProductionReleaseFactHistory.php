<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "production_release_fact_history".
 *
 * @property int $id
 * @property int|null $userId
 * @property int|null $releaseId
 * @property float|null $quantity
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property ProductionRelease $release
 */
class ProductionReleaseFactHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'production_release_fact_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userId', 'releaseId'], 'integer'],
            [['quantity'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['releaseId'], 'exist', 'skipOnError' => true, 'targetClass' => ProductionRelease::className(), 'targetAttribute' => ['releaseId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'userId' => Yii::t('app', 'User ID'),
            'releaseId' => Yii::t('app', 'Release ID'),
            'quantity' => Yii::t('app', 'Quantity'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[Release]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRelease()
    {
        return $this->hasOne(ProductionRelease::className(), ['id' => 'releaseId']);
    }
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }
}
