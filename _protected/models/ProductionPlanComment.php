<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "production_plan_comment".
 *
 * @property int $id
 * @property int $production_plan_id
 * @property string $comment
 * @property int $created_at
 * @property int $created_by
 */
class ProductionPlanComment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'production_plan_comment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['comment', 'production_plan_id'], 'required'],
            [['production_plan_id', 'created_at', 'created_by'], 'integer'],
            [['comment'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'production_plan_id' => Yii::t('app', 'Production Plan ID'),
            'comment' => Yii::t('app', 'Comment'),
            'created_at' => Yii::t('app', 'Created at'),
            'created_by' => Yii::t('app', 'Created by'),
        ];
    }
}
