<?php
namespace app\enums;

use Yii;

class ContainerType {

  // CONTAINER TYPE LIST
  const CONTAINER_TYPE_20FT = '20ft';
  const CONTAINER_TYPE_40FT = '40ft';
  const CONTAINER_TYPE_40HC = '40hc';

  public static function list()
  :array {
    return [
      self::CONTAINER_TYPE_20FT => Yii::t('app', '20FT'),
      self::CONTAINER_TYPE_40FT => Yii::t('app', '40FT'),
      self::CONTAINER_TYPE_40HC => Yii::t('app', '40HC')
    ];
  }

  public static function name($containerType)
  :string {
    return self::list()[$containerType] ?? '';
  }

  public static $capacity = [
    self::CONTAINER_TYPE_20FT => 33.1,
    self::CONTAINER_TYPE_40FT => 67.5,
    self::CONTAINER_TYPE_40HC => 76.4
  ];

  public static $load = [
    self::CONTAINER_TYPE_20FT => 12000,
    self::CONTAINER_TYPE_40FT => 20000,
    self::CONTAINER_TYPE_40HC => 20000
  ];
  

}
