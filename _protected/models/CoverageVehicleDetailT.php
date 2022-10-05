<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "coverage_vehicle_detail_t".
 *
 * @property int $id
 * @property int $coverage_vehicle_id
 * @property string|null $type D - Daily, W - Weekly
 * @property int|null $col1
 * @property int|null $col2
 * @property int|null $col3
 * @property int|null $col4
 * @property int|null $col5
 * @property int|null $col6
 * @property int|null $col7
 * @property int|null $col8
 * @property int|null $col9
 * @property int|null $col10
 * @property int|null $col11
 * @property int|null $col12
 * @property int|null $col13
 * @property int|null $col14
 * @property int|null $col15
 * @property int|null $col16
 * @property int|null $col17
 * @property int|null $col18
 * @property int|null $col19
 * @property int|null $col20
 * @property int|null $col21
 * @property int|null $col22
 * @property int|null $col23
 * @property int|null $col24
 * @property int|null $col25
 * @property int|null $col26
 * @property int|null $col27
 * @property int|null $col28
 * @property int|null $col29
 * @property int|null $col30
 * @property int|null $col31
 * @property int|null $col32
 * @property int|null $col33
 * @property int|null $col34
 * @property int|null $col35
 * @property int|null $col36
 * @property int|null $col37
 * @property int|null $col38
 * @property int|null $col39
 * @property int|null $col40
 * @property int|null $col41
 * @property int|null $col42
 * @property int|null $col43
 * @property int|null $col44
 * @property int|null $col45
 * @property int|null $col46
 * @property int|null $col47
 * @property int|null $col48
 * @property int|null $col49
 * @property int|null $col50
 * @property int|null $col51
 * @property int|null $col52
 * @property int|null $col53
 * @property int|null $col54
 * @property int|null $col55
 * @property int|null $col56
 * @property int|null $col57
 * @property int|null $col58
 * @property int|null $col59
 * @property int|null $col60
 * @property int|null $col61
 * @property int|null $col62
 * @property int|null $col63
 * @property int|null $col64
 * @property int|null $col65
 * @property int|null $col66
 * @property int|null $col67
 * @property int|null $col68
 * @property int|null $col69
 * @property int|null $col70
 * @property int|null $col71
 */
class CoverageVehicleDetailT extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'coverage_vehicle_detail_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['coverage_vehicle_id'], 'required'],
            [['coverage_vehicle_id', 'col1', 'col2', 'col3', 'col4', 'col5', 'col6', 'col7', 'col8', 'col9', 'col10', 'col11', 'col12', 'col13', 'col14', 'col15', 'col16', 'col17', 'col18', 'col19', 'col20', 'col21', 'col22', 'col23', 'col24', 'col25', 'col26', 'col27', 'col28', 'col29', 'col30', 'col31', 'col32', 'col33', 'col34', 'col35', 'col36', 'col37', 'col38', 'col39', 'col40', 'col41', 'col42', 'col43', 'col44', 'col45', 'col46', 'col47', 'col48', 'col49', 'col50', 'col51', 'col52', 'col53', 'col54', 'col55', 'col56', 'col57', 'col58', 'col59', 'col60', 'col61', 'col62', 'col63', 'col64', 'col65', 'col66', 'col67', 'col68', 'col69', 'col70', 'col71'], 'integer'],
            [['type'], 'string', 'max' => 2],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'coverage_vehicle_id' => Yii::t('app', 'Coverage Vehicle ID'),
            'type' => Yii::t('app', 'Type'),
            'col1' => Yii::t('app', 'Col1'),
            'col2' => Yii::t('app', 'Col2'),
            'col3' => Yii::t('app', 'Col3'),
            'col4' => Yii::t('app', 'Col4'),
            'col5' => Yii::t('app', 'Col5'),
            'col6' => Yii::t('app', 'Col6'),
            'col7' => Yii::t('app', 'Col7'),
            'col8' => Yii::t('app', 'Col8'),
            'col9' => Yii::t('app', 'Col9'),
            'col10' => Yii::t('app', 'Col10'),
            'col11' => Yii::t('app', 'Col11'),
            'col12' => Yii::t('app', 'Col12'),
            'col13' => Yii::t('app', 'Col13'),
            'col14' => Yii::t('app', 'Col14'),
            'col15' => Yii::t('app', 'Col15'),
            'col16' => Yii::t('app', 'Col16'),
            'col17' => Yii::t('app', 'Col17'),
            'col18' => Yii::t('app', 'Col18'),
            'col19' => Yii::t('app', 'Col19'),
            'col20' => Yii::t('app', 'Col20'),
            'col21' => Yii::t('app', 'Col21'),
            'col22' => Yii::t('app', 'Col22'),
            'col23' => Yii::t('app', 'Col23'),
            'col24' => Yii::t('app', 'Col24'),
            'col25' => Yii::t('app', 'Col25'),
            'col26' => Yii::t('app', 'Col26'),
            'col27' => Yii::t('app', 'Col27'),
            'col28' => Yii::t('app', 'Col28'),
            'col29' => Yii::t('app', 'Col29'),
            'col30' => Yii::t('app', 'Col30'),
            'col31' => Yii::t('app', 'Col31'),
            'col32' => Yii::t('app', 'Col32'),
            'col33' => Yii::t('app', 'Col33'),
            'col34' => Yii::t('app', 'Col34'),
            'col35' => Yii::t('app', 'Col35'),
            'col36' => Yii::t('app', 'Col36'),
            'col37' => Yii::t('app', 'Col37'),
            'col38' => Yii::t('app', 'Col38'),
            'col39' => Yii::t('app', 'Col39'),
            'col40' => Yii::t('app', 'Col40'),
            'col41' => Yii::t('app', 'Col41'),
            'col42' => Yii::t('app', 'Col42'),
            'col43' => Yii::t('app', 'Col43'),
            'col44' => Yii::t('app', 'Col44'),
            'col45' => Yii::t('app', 'Col45'),
            'col46' => Yii::t('app', 'Col46'),
            'col47' => Yii::t('app', 'Col47'),
            'col48' => Yii::t('app', 'Col48'),
            'col49' => Yii::t('app', 'Col49'),
            'col50' => Yii::t('app', 'Col50'),
            'col51' => Yii::t('app', 'Col51'),
            'col52' => Yii::t('app', 'Col52'),
            'col53' => Yii::t('app', 'Col53'),
            'col54' => Yii::t('app', 'Col54'),
            'col55' => Yii::t('app', 'Col55'),
            'col56' => Yii::t('app', 'Col56'),
            'col57' => Yii::t('app', 'Col57'),
            'col58' => Yii::t('app', 'Col58'),
            'col59' => Yii::t('app', 'Col59'),
            'col60' => Yii::t('app', 'Col60'),
            'col61' => Yii::t('app', 'Col61'),
            'col62' => Yii::t('app', 'Col62'),
            'col63' => Yii::t('app', 'Col63'),
            'col64' => Yii::t('app', 'Col64'),
            'col65' => Yii::t('app', 'Col65'),
            'col66' => Yii::t('app', 'Col66'),
            'col67' => Yii::t('app', 'Col67'),
            'col68' => Yii::t('app', 'Col68'),
            'col69' => Yii::t('app', 'Col69'),
            'col70' => Yii::t('app', 'Col70'),
            'col71' => Yii::t('app', 'Col71'),
        ];
    }
}
