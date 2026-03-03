<?php
namespace app\enums;

use Yii;

class CargoType {

  // CARGO TYPE LIST

  const CARGO_TYPE_KD = 1;
  const CARGO_TYPE_RAW = 2;
  const CARGO_TYPE_COIL = 3;
  const CARGO_TYPE_EQUIPMENT = 4;
  const CARGO_TYPE_DGCARGO = 5;
  const CARGO_TYPE_FABMAT = 6;
  const CARGO_TYPE_CONSOL = 7;

  public static function list()
  :array {
    return [
      self::CARGO_TYPE_KD => Yii::t('app', 'KD'),
      self::CARGO_TYPE_RAW => Yii::t('app', 'RAW'),
      self::CARGO_TYPE_COIL => Yii::t('app', 'COIL'),
      self::CARGO_TYPE_EQUIPMENT => Yii::t('app', 'EQUIPMENT'),
      self::CARGO_TYPE_DGCARGO => Yii::t('app', 'DG/Cargo'),
      self::CARGO_TYPE_FABMAT => Yii::t('app', 'Fab/Mat'),
      self::CARGO_TYPE_CONSOL => Yii::t('app', 'Consolidated')
    ];
  }

  public static function listDesc()
  :array {
    return [
      self::CARGO_TYPE_KD => Yii::t('app', 'KD'),
      self::CARGO_TYPE_RAW => Yii::t('app', 'RAW'),
      self::CARGO_TYPE_COIL => Yii::t('app', 'COIL'),
      self::CARGO_TYPE_EQUIPMENT => Yii::t('app', 'EQUIPMENT'),
      self::CARGO_TYPE_DGCARGO => Yii::t('app', 'DG/Cargo'),
      self::CARGO_TYPE_FABMAT => Yii::t('app', 'Fab/Mat'),
      self::CARGO_TYPE_CONSOL => Yii::t('app', 'Consolidated')
    ];
  }

  public static function name($cargoType)
  :string {
    return self::list()[$cargoType] ?? '';
  }

  public static function desc($cargoType)
  :string {
    return self::listDesc()[$cargoType] ?? '';
  }

}
