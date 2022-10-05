<?php
namespace app\enums;

use Yii;

class FreightInvoiceType {

  // FREIGHT TYPE LIST
  const FREIGHT_TYPE_INBOUND = 0;
  const FREIGHT_TYPE_OUTBOUND = 1;

  public static function list()
  :array {
    return [
      self::FREIGHT_TYPE_INBOUND => Yii::t('app', 'Inbound'),
      self::FREIGHT_TYPE_OUTBOUND => Yii::t('app', 'Outbound'),
    ];
  }

  public static function name($freightType)
  :string {
    return self::list()[$freightType];
  }

}
