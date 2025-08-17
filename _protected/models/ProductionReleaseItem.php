<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "production_release_item".
 *
 * @property int $id
 * @property int|null $release_id
 * @property float|null $qty
 * @property string|null $comment
 * @property int|null $status
 * @property string|null $created
 * @property string|null $updated
 * @property int|null $partId
 *
 * @property ProductionRelease $release
 */
class ProductionReleaseItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'production_release_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['release_id', 'status', 'partId'], 'integer'],
            [['qty'], 'number'],
            [['created', 'updated'], 'safe'],
            [['comment'], 'string', 'max' => 255],
            [['release_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductionRelease::className(), 'targetAttribute' => ['release_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'release_id' => Yii::t('app', 'Release ID'),
            'qty' => Yii::t('app', 'Qty'),
            'comment' => Yii::t('app', 'Comment'),
            'status' => Yii::t('app', 'Status'),
            'created' => Yii::t('app', 'Created'),
            'updated' => Yii::t('app', 'Updated'),
            'partId' => Yii::t('app', 'Part ID'),
        ];
    }

    /**
     * Gets query for [[Release]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRelease()
    {
        return $this->hasOne(ProductionRelease::className(), ['id' => 'release_id']);
    }
}
