<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "req_detail_plan".
 *
 * @property int $id
 * @property int $req_id
 * @property string $type D - Daily, W - Weekly
 * @property string $col1
 * @property string $col2
 * @property string $col3
 * @property string $col4
 * @property string $col5
 * @property string $col6
 * @property string $col7
 * @property string $col8
 * @property string $col9
 * @property string $col10
 * @property string $col11
 * @property string $col12
 * @property string $col13
 * @property string $col14
 * @property string $col15
 * @property string $col16
 * @property string $col17
 * @property string $col18
 * @property string $col19
 * @property string $col20
 * @property string $col21
 * @property string $col22
 * @property string $col23
 * @property string $col24
 * @property string $col25
 * @property string $col26
 * @property string $col27
 * @property string $col28
 * @property string $col29
 * @property string $col30
 * @property string $col31
 * @property string $col32
 * @property string $col33
 * @property string $col34
 * @property string $col35
 * @property string $col36
 * @property string $col37
 * @property string $col38
 * @property string $col39
 * @property string $col40
 * @property string $col41
 * @property string $col42
 * @property string $col43
 * @property string $col44
 * @property string $col45
 * @property string $col46
 * @property string $col47
 * @property string $col48
 * @property string $col49
 * @property string $col50
 * @property string $col51
 * @property string $col52
 * @property string $col53
 * @property string $col54
 * @property string $col55
 * @property string $col56
 * @property string $col57
 * @property string $col58
 * @property string $col59
 * @property string $col60
 * @property string $col61
 * @property string $col62
 * @property string $col63
 * @property string $col64
 * @property string $col65
 * @property string $col66
 * @property string $col67
 * @property string $col68
 * @property string $col69
 * @property string $col70
 * @property string $col71
 *
 * @property Req $req
 */
class ReqDetailPlan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'req_detail_plan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['req_id'], 'required'],
            [['req_id'], 'integer'],
            [['col1', 'col2', 'col3', 'col4', 'col5', 'col6', 'col7', 'col8', 'col9', 'col10', 'col11', 'col12', 'col13', 'col14', 'col15', 'col16', 'col17', 'col18', 'col19', 'col20', 'col21', 'col22', 'col23', 'col24', 'col25', 'col26', 'col27', 'col28', 'col29', 'col30', 'col31', 'col32', 'col33', 'col34', 'col35', 'col36', 'col37', 'col38', 'col39', 'col40', 'col41', 'col42', 'col43', 'col44', 'col45', 'col46', 'col47', 'col48', 'col49', 'col50', 'col51', 'col52', 'col53', 'col54', 'col55', 'col56', 'col57', 'col58', 'col59', 'col60', 'col61', 'col62', 'col63', 'col64', 'col65', 'col66', 'col67', 'col68', 'col69', 'col70', 'col71'], 'number'],
            [['type'], 'string', 'max' => 2],
            [['req_id'], 'exist', 'skipOnError' => true, 'targetClass' => Req::className(), 'targetAttribute' => ['req_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'req_id' => Yii::t('app', 'Req ID'),
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReq()
    {
        return $this->hasOne(Req::class, ['id' => 'req_id']);
    }
}
