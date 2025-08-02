<?php
namespace app\enums;

use Yii;

class FreightInvoicePaymentType {

  const TYPE_TARIFF_AMOUNT = 1;
  const TYPE_TRANSPORTATION_COST = 2;
  const TYPE_INSURANCE_COST = 3;
  const TYPE_ADDITIONAL_COST = 4;
  const TYPE_FREIGHT_DOCUMENT_PROCESSING = 5;
  const TYPE_TERMINAL = 6;
  const TYPE_CONTAINER_RETURN = 7;
  const TYPE_REVERSE_LOGISTICS_BORDER = 8;

  public static function list()
  :array {
    return [
      self::TYPE_TARIFF_AMOUNT => Yii::t('app', 'Tariff amount'),
      self::TYPE_TRANSPORTATION_COST => Yii::t('app', 'Transportation cost'),
      self::TYPE_INSURANCE_COST => Yii::t('app', 'Insurance cost'),
      self::TYPE_ADDITIONAL_COST => Yii::t('app', 'Additional cost'),
      self::TYPE_FREIGHT_DOCUMENT_PROCESSING => Yii::t('app', 'Freight document processing'),
      self::TYPE_TERMINAL => Yii::t('app', 'Terminal'),
      self::TYPE_CONTAINER_RETURN => Yii::t('app', 'Container return'),
      self::TYPE_REVERSE_LOGISTICS_BORDER => Yii::t('app', 'Reverse logistics border'),
    ];
  }

  public static function listInOut()
  :array {
    return [
      self::TYPE_TARIFF_AMOUNT => 'both',
      self::TYPE_TRANSPORTATION_COST => 'out',
      self::TYPE_INSURANCE_COST => 'out',
      self::TYPE_ADDITIONAL_COST => 'out',
      self::TYPE_FREIGHT_DOCUMENT_PROCESSING => 'in',
      self::TYPE_TERMINAL => 'in',
      self::TYPE_CONTAINER_RETURN => 'in',
      self::TYPE_REVERSE_LOGISTICS_BORDER => 'in',
    ];
  }

  public static function name($index)
  :string {
    return self::list()[$index];
  }

  public static function inOut($index)
  :string {
    return self::listInOut()[$index];
  }

  

}
