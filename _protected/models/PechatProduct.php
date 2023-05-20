<?php

namespace app\models;

use Yii;
use app\models\Part;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "pechat_product".
 *
 * @property int $id
 * @property int|null $part_id
 * @property string|null $number_lot
 * @property string|null $date
 * @property int|null $weight_netto
 * @property int|null $weight_brutto
 */
class PechatProduct extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pechat_product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['part_id', 'number_lot', 'weight_netto', 'weight_brutto', 'date', 'line', 'color_id'], 'required'],
            [['part_id', 'weight_netto', 'weight_brutto', 'color_id'], 'integer'],
            [['date'], 'safe'],
            [['number_lot', 'line', 'comment'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'part_id' => Yii::t('app', 'Part ID'),
            'number_lot' => Yii::t('app', 'Lot №'),
            'date' => Yii::t('app', 'Date'),
            'weight_netto' => Yii::t('app', 'Netto'),
            'weight_brutto' => Yii::t('app', 'Brutto'),
            'line' => Yii::t('app', 'Line'),
            'color_id' => Yii::t('app', 'Color'),
            'comment' => Yii::t('app', 'Comment'),
        ];
    }
    public function getPart()
    {
        return $this->hasOne(Part::className(), ['id' => 'part_id']);
    }
    public static function getPartsList()
    {
        $parts = Part::find()
            ->select(['id', 'CONCAT(part_name, " ", part_no) AS part_name'])
            ->all();
        $items = ArrayHelper::map($parts, 'id', 'part_name');
        return $items;
    }
    public  function getPartColor()
    {
        return $this->hasOne(PartColor::className(), ['id' => 'color_id']);
    }
    public static function getPartColorList()
    {
        $parts = PartColor::find()
            ->select(['id', 'name'])
            ->all();
        $items = ArrayHelper::map($parts, 'id', 'name');
        return $items;
    } 

}
