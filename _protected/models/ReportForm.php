<?php
namespace app\models;

use yii\base\Model;
use Yii;

/**
 * LoginForm is the model behind the login form.
 */
class ReportForm extends Model
{
    public $from1,$to1,$from2,$to2,$shop,$shop2,$company_id;

    public function rules()
    {
        return [
            [['from1', 'to1','from2', 'to2'], 'required'],
            [['shop','shop2','company_id'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'from1' => Yii::t('app', 'From'),
            'to1' => Yii::t('app', 'To'),
            'from2' => Yii::t('app', 'From'),
            'to2' => Yii::t('app', 'To'),
            'shop' => Yii::t('app', 'Shop'),
            'shop2' => Yii::t('app', 'Shop'),
            'company_id' => Yii::t('app', 'Company'),
        ];
    }

    
}
