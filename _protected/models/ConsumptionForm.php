<?php
namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 */
class ConsumptionForm extends Model{

    public $serial_number;

    public function rules()
    {
        return [
            [['serial_number'],'required']
        ];
    }

    
    public function attributeLabels()
    {
        return [
            'serial_number' => Yii::t('app', 'Serial number')
        ];
    }

    
}
