<?php
namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "recept_control".
 *
 * @property int      $id
 * @property string   $path
 * @property integer   $post_id
 * @property int      $created_at
 * @property int|null $updated_at
 */
class PostImages extends \yii\db\ActiveRecord {


  /**
   * {@inheritdoc}
   */
  public static function tableName() {
    return 'post_images';
  }

  public function behaviors() {
    return [
      TimestampBehavior::className(),
      BlameableBehavior::className(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['path', 'post_id'], 'required'],
      [['path'], 'string'],
      [['post_id'], 'integer'],

    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels() {
    return [
      'id' => Yii::t('app', 'ID'),
      'post_id' => Yii::t('app', 'Post id'),
      'path' => Yii::t('app', 'Path'),
      'created_at' => Yii::t('app', 'Created at'),
      'created_by' => Yii::t('app', 'Created by'),
      'updated_by' => Yii::t('app', 'Updated by'),
      'updated_at' => Yii::t('app', 'Updated at'),
    ];
  }


  /**
   * @return ActiveQuery
   */
  public function getCreatedBy() {
    return $this->hasOne(User::className(), ['id' => 'created_by']);
  }

  /**
   * @return ActiveQuery
   */
  public function getUpdatedBy() {
    return $this->hasOne(User::className(), ['id' => 'updated_by']);
  }

  public function getUpdatedAtFormatted() {
    return (!empty($this->updated_at)) ? date('d.m.Y H:i', $this->updated_at) : '';
  }

  public function getCreatedAtFormatted() {
    return (!empty($this->created_at)) ? date('d.m.Y H:i', $this->created_at) : '';
  }

}
