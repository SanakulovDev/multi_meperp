<?php
namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 */
class ProduceForm extends Model{

    public $serial_number;
    public $qty;

    public function rules()
    {
        return [
            [['serial_numbers'],'required'],
            [['qty'],'safe'],
        ];
    }

    
    public function attributeLabels()
    {
        return [
            'serial_number' => Yii::t('app', 'Serial number'),
            'qty' => Yii::t('app', 'Quantity'),
        ];
    }

    
}
