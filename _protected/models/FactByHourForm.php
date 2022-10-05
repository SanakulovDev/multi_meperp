<?php

namespace app\models;

use Yii;
use yii\base\Model;

class FactByHourForm extends Model {

    public $flocOrLine;
    public $floc;
    public $line;
    public $todayOrYesterday;
    public $shift;

    public function rules() {
        return [
            [['flocOrLine','todayOrYesterday','shift','floc','line'], 'safe'],
        ];
    }

    public function attributeLabels() {
        return [
            'flocOrLine' => Yii::t('app', 'FLOC/Line'),
            'floc' => Yii::t('app', 'FLOC'),
            'line' => Yii::t('app', 'Line'),
//            'line' => Yii::t('app', 'Line'),
            'todayOrYesterday' => Yii::t('app', 'Today/Yesterday'),
            'shift' => Yii::t('app', 'Shift'),
        ];
    }

}
