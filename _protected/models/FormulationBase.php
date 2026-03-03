<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "formulation_base".
 *
 * @property int $id
 * @property int $part_id
 * @property float $pack
 * @property int $version
 * @property int $status
 * @property float $std_rate
 * @property string $items
 * @property string $specifications
 * @property string $instructions
 *
 * @property Part $part
 */
class FormulationBase extends \yii\db\ActiveRecord
{
    // the list of status values that can be stored in user table
    const STATUS_ACTIVE   = 10;
    const STATUS_INACTIVE = 1;
    //const STATUS_DELETED  = 0;

    /**
     * List of names for each status.
     * @var array
     */
    public $statusList = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
        //self::STATUS_DELETED  => 'Deleted'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'formulation_base';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['part_id', 'pack', 'version', 'status', 'std_rate', 'items', 'specifications', 'instructions'], 'required'],
            [['part_id', 'version', 'status'], 'integer'],
            [['pack', 'std_rate'], 'number'],
            [['items', 'specifications', 'instructions'], 'string'],
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
            'part_id' => 'Part',
            'pack' => 'Pack',
            'version' => 'Version',
            'status' => 'Status',
            'std_rate' => 'Std Rate',
            'items' => 'Items',
            'specifications' => 'Specifications',
            'instructions' => 'Instructions',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPart()
    {
        return $this->hasOne(Part::className(), ['id' => 'part_id']);
    }

    public function getBasename()
    {
        return implode('-', [$this->part->partinfo, ($this->pack+0), ($this->version+0) ] );
    }
}
