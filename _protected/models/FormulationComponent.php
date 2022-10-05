<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "formulation_component".
 *
 * @property int $id
 * @property int $formulation_id
 * @property int $part_id
 * @property float $std_value
 * @property float $actual_value
 *
 * @property Formulation $formulation
 * @property Part $part
 */
class FormulationComponent extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'formulation_component';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['formulation_id', 'part_id', 'std_value', 'actual_value'], 'required'],
            [['formulation_id', 'part_id'], 'integer'],
            [['std_value', 'actual_value'], 'number'],
            [['formulation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Formulation::className(), 'targetAttribute' => ['formulation_id' => 'id']],
            [['part_id'], 'exist', 'skipOnError' => true, 'targetClass' => Part::className(), 'targetAttribute' => ['part_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'formulation_id' => 'Formulation ID',
            'part_id' => 'Part ID',
            'std_value' => 'Std Value',
            'actual_value' => 'Actual Value',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFormulation()
    {
        return $this->hasOne(Formulation::className(), ['id' => 'formulation_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPart()
    {
        return $this->hasOne(Part::className(), ['id' => 'part_id']);
    }
}
