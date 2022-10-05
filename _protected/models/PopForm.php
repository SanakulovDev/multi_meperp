<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * UploadForm is the model behind the upload form.
 */
class PopForm extends Model {

    public $part_id;
    
    public function rules() {
        return [
            [['part_id'], 'safe'],
        ];
    }

    public function attributeLabels() {
        return [
            'part_id' => Yii::t('app', 'Part'),
        ];
    }

    

}
