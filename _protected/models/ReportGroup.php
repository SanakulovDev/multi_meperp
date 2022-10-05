<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "report_group".
 *
 * @property int $id
 * @property string $name
 * @property int|null $order
 * @property string|null $icon
 * @property string|null $color
 *
 * @property Report[] $reports
 */
class ReportGroup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'report_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['order'], 'integer'],
            [['name', 'icon', 'color'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'order' => Yii::t('app', 'Order'),
            'icon' => Yii::t('app', 'Icon'),
            'color' => Yii::t('app', 'Color'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReports()
    {
        return $this->hasMany(Report::className(), ['report_group_id' => 'id']);
    }
}
