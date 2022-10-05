<?php /** @noinspection PropertiesInspection */
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "sequence".
 *
 * @property int    $id
 * @property string $code
 * @property int    $last_seq
 * @property string $description
 */
class Sequence extends ActiveRecord {

  /** SequenceType list: */
  //TYPE_SUPPLY = 'supply' - Label chiqarish uchun
  public const TYPE_SUPPLY = 'supply';

  //TYPE_BOMVERSION = 'bomVersion' - BOM versiyasi uchun
  public const TYPE_BOMVERSION = 'bomVersion';

  /**
   * {@inheritdoc}
   */
  public static function tableName() {
    return 'sequence';
  }

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['code', 'last_seq', 'description'], 'required'],
      [['last_seq'], 'integer'],
      [['code'], 'string', 'max' => 20],
      [['description'], 'string', 'max' => 100],
      [['code'], 'unique'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels() {
    return [
      'id' => Yii::t('app', 'ID'),
      'code' => Yii::t('app', 'Code'),
      'last_seq' => Yii::t('app', 'Last Seq'),
      'description' => Yii::t('app', 'Description'),
    ];
  }

  /**
   * Get last sequence - Bu berilgan sequence tip bo`yicha ohirgi sequenceni qaytaradi.
   * $seqType quyidagilarni qabul qiladi:
   * - self::TYPE_SUPPLY = 'supply': (Label chiqarish uchun)
   * - self::TYPE_BOMVERSION = 'bomVersion': (BOM versiyasi uchun)
   *
   * @return $msg = array('sts', 'msg', 'sequence')
   */
  public static function getLastSequence(string $seqType = null) {
    if ($seqType == null) {
      return [
        'sts' => 'BAD',
        'msg' => Yii::t('app',
                        '<strong>{nameAttribute}</strong> cannot be blank.',
                        ['nameAttribute' => $seqType,])
      ];
    }
    $lastSequence = (self::findOne(['code' => $seqType])->last_seq) ?? 0;

    return ['sts' => 'OK', 'sequence' => $lastSequence];
  }

  /**
   * Set last sequence - Bu berilgan sequence tip bo`yicha sequence table ga last_seq qo`shadi.
   * $seqType quyidagilarni qabul qiladi:
   * - self::TYPE_SUPPLY = 'supply': (Label chiqarish uchun)
   * - self::TYPE_BOMVERSION = 'bomVersion': (BOM versiyasi uchun)
   *
   * @return $msg = array('sts', 'msg', 'sequence')
   */
  public static function setLastSequence(string $seqType = null, int $version = 0) {
    if ($seqType == null) {
      return [
        'sts' => 'BAD',
        'msg' => Yii::t('app',
                        '<strong>{nameAttribute}</strong> cannot be blank.',
                        ['nameAttribute' => $seqType,])
      ];
    }
    $modelSequence = self::findOne(['code' => $seqType]);
    $modelSequence->last_seq = $version;
    if (!$modelSequence->save()) {
      return [
        'sts' => 'BAD',
        'msg' => Yii::t('app',
                        'Error-getLastSequence:<br> '.$modelSequence->errors
        )
      ];
    }

    return ['sts' => 'OK', 'sequence' => $modelSequence->last_seq];
  }

}

