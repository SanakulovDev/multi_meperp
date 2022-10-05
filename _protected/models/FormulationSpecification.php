<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "formulation_specification".
 *
 * @property int $id
 * @property int $formulation_id
 * @property string $item
 * @property float|null $min
 * @property float|null $max
 * @property float|null $result
 *
 * @property Formulation $formulation
 */
class FormulationSpecification extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'formulation_specification';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['formulation_id', 'item'], 'required'],
            [['formulation_id'], 'integer'],
            [['min', 'max', 'result'], 'number'],
            [['item'], 'string', 'max' => 100],
            [['formulation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Formulation::className(), 'targetAttribute' => ['formulation_id' => 'id']],
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
            'item' => 'Item',
            'min' => 'Min',
            'max' => 'Max',
            'result' => 'Result',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFormulation()
    {
        return $this->hasOne(Formulation::className(), ['id' => 'formulation_id']);
    }
}
