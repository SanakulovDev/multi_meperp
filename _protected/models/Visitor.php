<?php
namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "visitor".
 *
 * @property int    $id
 * @property int    $user_id
 * @property string $controller
 * @property string $action
 * @property string $user_ip
 * @property string $visited_at
 * @property User   $user
 */
class Visitor extends ActiveRecord {

  /**
   * {@inheritdoc}
   */
  public static function tableName() {
    return 'visitor';
  }

  /**
   * {@inheritdoc}
   */
  public $filter_from, $filter_to, $page;

  public function rules() {
    return [
      [['user_id'], 'integer'],
      [['visited_at'], 'safe'],
      [['controller', 'action', 'user_ip'], 'string', 'max' => 255],
      [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels() {
    return [
      'id' => Yii::t('app', 'ID'),
      'user_id' => Yii::t('app', 'User'),
      'controller' => Yii::t('app', 'Controller'),
      'action' => Yii::t('app', 'Action'),
      'user_ip' => Yii::t('app', 'IP address'),
      'visited_at' => Yii::t('app', 'Visited at'),
      'page' => Yii::t('app', 'Page'),
      'filter_from' => Yii::t('app', 'From'),
      'filter_to' => Yii::t('app', 'To'),
    ];
  }

  /**
   * @return ActiveQuery
   */
  public function getUser() {
    return $this->hasOne(User::className(), ['id' => 'user_id']);
  }

  public function getPageroute() {
    return $this->controller.'/'.$this->action;
  }

}
