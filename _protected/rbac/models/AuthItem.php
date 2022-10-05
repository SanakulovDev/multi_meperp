<?php

namespace app\rbac\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "auth_item".
 *
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $rule_name
 * @property string $data
 * @property integer $created_at
 * @property integer $updated_at
 */
class AuthItem extends ActiveRecord
{
  /**
   * Declares the name of the database table associated with this AR class.
   *
   * @return string
   */
  public static function tableName()
  {
    return '{{%auth_item}}';
  }

  const TYPE_ROLE = 1;
  const TYPE_PERMISSION = 2;
  public $permissionList;

  public function listTypes()
  {
    return [
      1 => Yii::t('app', 'Role'),
      2 => Yii::t('app', 'Permission')
    ];
  }

  public function behaviors()
  {
    return [
      TimestampBehavior::className(),
    ];
  }

  public function rules()
  {
    return [
      [['name', 'type'], 'required'],
      [['type', 'created_at'], 'integer'],
      [['name','rule_name'], 'string', 'max' => 64],
      [['description','data'], 'string', 'max' => 64],
    ];
  }

  public function attributeLabels()
  {
    return [
      'name' => Yii::t('app', 'Name'),
      'type' => Yii::t('app', 'Type'),
      'description' => Yii::t('app', 'Description'),
      'rule_name' => Yii::t('app', 'Rule name'),
      'data' => Yii::t('app', 'Data'),
      'created_at' => Yii::t('app', 'Created at'),
      'updated_at' => Yii::t('app', 'Updated at'),
      'permissionList' => Yii::t('app', 'Permissions'),
    ];
  }

  public function getLongName()
  {
    return $this->name.' '.$this->description;
  }
  public function getCreatedAtFormatted(){
    return (!empty($this->created_at)) ? date('d.m.Y H:i', $this->created_at) : '';
  }

  public function getUpdatedAtFormatted(){
    return (!empty($this->updated_at)) ? date('d.m.Y H:i', $this->updated_at) : '';
  }

  public function getPermissions(){
    return $this->hasMany(AuthItem::className(), ['name' => 'child'])->viaTable('auth_item_child', ['parent' => 'name']);
  }

  /**
   * Returns roles.
   * NOTE: used in user/index and user/update.
   *
   * @return array|\yii\db\ActiveRecord[]
   */
  public static function getRoles()
  {
    // if user is not 'theCreator' ( You ), we do not want to show him users with that role
    if (!Yii::$app->user->can('theCreator')) {
      return static::find()->select('name')->where(['type' => 1])->andWhere(['!=', 'name', 'theCreator'])->all();
    }

    // this is You or some other super admin, so show everything
    return static::find()->select('name')->where(['type' => 1])->all();
  }
}
