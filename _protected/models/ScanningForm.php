<?php
namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 */
class ScanningForm extends Model{

    public $barcode,$docdate,$barCodeData;

    public function rules()
    {
        return [
            [['barcode', 'docdate', 'barCodeData'],'safe']
        ];
    }

    
    public function attributeLabels()
    {
        return [
            'barcode' => Yii::t('app', 'Barcode'),
            'barCodeData' => Yii::t('app', 'Barcode data'),
            'docdate' => Yii::t('app', 'Document date')
        ];
    }

    
}
