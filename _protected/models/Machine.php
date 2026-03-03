<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\Response;

/**
 * This is the model class for table "machine".
 *
 * @property int $id
 * @property int $product_line_id Зона
 * @property string $no
 * @property string|null $title
 * @property int $last_count
 * @property int $mold_id
 * @property int $sequence
 * @property int $status
 * @property int $created_by
 * @property int $created_at
 * @property int $updated_by
 * @property int $updated_at
 *
 * @property User $createdBy
 * @property Mold $mold
 * @property ProductLine $productLine
 * @property User $updatedBy
 * @property MoldMachine[] $moldMachines
 * @property Mold[] $molds
 */
class Machine extends ActiveRecord{

	// the list of status values that can be stored in user table
	const STATUS_ACTIVE = 1;
	const STATUS_INACTIVE = 0;

	/**
	 * List of names for each status.
	 * @var array
	 */
	public $statusList = [
		self::STATUS_ACTIVE => 'Актив',
		self::STATUS_INACTIVE => 'Не актив',
  ];
  
  /**
   * {@inheritdoc}
   */
  public static function tableName(){
    return 'machine';
  }

  /**
   * {@inheritdoc}
   */
  public function rules(){
    return [
      [['product_line_id', 'no', 'created_by', 'created_at'], 'required'],
      [['product_line_id', 'last_count', 'mold_id', 'sequence', 'status', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
      [['no'], 'string', 'max' => 50],
      [['title'], 'string', 'max' => 100],
      [['last_count'], 'default', 'value' => 0],
      [['product_line_id', 'no'], 'unique', 'targetAttribute' => ['product_line_id', 'no']],
      [['product_line_id', 'sequence'], 'unique', 'targetAttribute' => ['product_line_id', 'sequence']],
      [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
      [['mold_id'], 'exist', 'skipOnError' => true, 'targetClass' => Mold::className(), 'targetAttribute' => ['mold_id' => 'id']],
      [['product_line_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductLine::className(), 'targetAttribute' => ['product_line_id' => 'id']],
      [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels(){
    return [
      'id' => Yii::t('app', 'ID'),
      'product_line_id' => Yii::t('app', 'Зона'),
      'no' => Yii::t('app', 'Machine no'),
      'title' => Yii::t('app', 'Title'),
      'last_count' => Yii::t('app', 'Last Count'),
      'mold_id' => Yii::t('app', 'Mold'),
      'sequence' => Yii::t('app', 'Sequence'),
      'status' => Yii::t('app', 'Status'),
      'created_by' => Yii::t('app', 'Created by'),
      'created_at' => Yii::t('app', 'Created at'),
      'updated_by' => Yii::t('app', 'Updated by'),
      'updated_at' => Yii::t('app', 'Updated at'),
    ];
  }

  /**
   * Returns a list of behaviors that this component should behave as.
   * @return array
   */
  public function behaviors(){
    return [
      TimestampBehavior::className(),
      BlameableBehavior::className(),
    ];
  }

  /**
   * @return ActiveQuery
   */
  public function getCreatedBy(){
    return $this->hasOne(User::className(), ['id' => 'created_by']);
  }

  /**
   * @return ActiveQuery
   */
  public function getMold(){
    return $this->hasOne(Mold::className(), ['id' => 'mold_id']);
  }

  /**
   * @return ActiveQuery
   */
  public function getProductLine(){
    return $this->hasOne(ProductLine::className(), ['id' => 'product_line_id']);
  }


  public function getUpdatedBy(){
    return $this->hasOne(User::className(), ['id' => 'updated_by']);
  }

  public function getUpdatedAtFormatted()
  {
    return (!empty($this->updated_at)) ? date('d.m.Y H:i', $this->updated_at) : '';
  }

  public function getCreatedAtFormatted()
  {
    return (!empty($this->created_at)) ? date('d.m.Y H:i', $this->created_at) : '';
  }

  public function getMoldMachines(){
    return $this->hasMany(MoldMachine::className(), ['machine_id' => 'id']);
  }

  public function getMolds(){
    return $this->hasMany(Mold::className(), ['id' => 'mold_id'])->viaTable('mold_machine', ['machine_id' => 'id']);
  }

}
