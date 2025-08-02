<?php
namespace app\components;

use app\models\Currency;
use app\models\CurrencyRate;
use Da\QrCode\Contracts\ErrorCorrectionLevelInterface;
use Da\QrCode\QrCode;
use DateInterval;
use DatePeriod;
use DateTime;
use Yii;

class Helpers {

  public static function getPeriod($date = null) {
    if($date) {
      $today = date('Y-m-d', strtotime($date));
      $hours = date('H', strtotime($date));
    } else {
      $today = $date ?? date('Y-m-d');
      $hours = date('H');
    }
    $shift = 1;
    $start_at = '08:00';
    $end_at = '20:00';
    $productionDate = $today;
    if($hours < 8) {
      $shift = 2;
      $productionDate = date('Y-m-d', strtotime($today.' -1 days'));
      $start_at = date('Y-m-d 20:00', strtotime($today.' -1 days'));
      $end_at = $today.' 08:00';
    } elseif($hours >= 20) {
      $shift = 2;
      $start_at = $today.' 20:00';
      $end_at = date('Y-m-d 08:00', strtotime($today.' +1 days'));
    } else {
      $shift = 1;
      $start_at = $today.' 08:00';
      $end_at = $today.' 20:00';
    }
    return compact('shift', 'productionDate', 'start_at', 'end_at');
  }

  public static function getShift($date = null) {
    $today = $date ?? date('Y-m-d H:i');
    $shifts = Yii::$app->params['shifts'];
    $shift = null;
    $productionDate = null;
    $start_at = null;
    $end_at = null;
    foreach($shifts as $key => $val) {
      if(is_array($val[0])) {
        foreach($val as $item) {
          if(self::checkPeriod($item[0], $item[1], $today)) {
            $shift = $key;
            $productionDate = date('Y-m-d', strtotime($today.$item[2]));
            $start_at = $productionDate.' '.$item[0];
            $end_at = $productionDate.' '.$item[1];
            break;
          }
        }
      } else {
        if(self::checkPeriod($val[0], $val[1], $today)) {
          $shift = $key;
          $productionDate = date('Y-m-d', strtotime($today.$val[2]));
          $start_at = $productionDate.' '.$val[0];
          $end_at = $productionDate.' '.$val[1];
          break;
        }
      }
    }
    return compact('shift', 'productionDate', 'start_at', 'end_at');
  }

  public static function checkPeriod($start, $end, $date) {
    $dateHi = date('H:i', strtotime($date));
    return $dateHi >= $start && $dateHi < $end;
  }

  // Ushbu funcksiya oxirgi 12 oy ni YYYY-MM formatida qaytaradi
  public static function getLast12Monthes() {

    $from = date('Y-m-01', strtotime('-11 month'));
    $to = date('Y-m-t');

    $begin = new DateTime($from);
    $end = new DateTime($to);
    $end = $end->modify('+1 day');
    $daterange = new DatePeriod($begin, new DateInterval('P1M'), $end);
    $period = [];
    foreach($daterange as $date) {
      $period[] = $date->format("Y-m");
    }

    return $period;
  }

  // Ushbu funcksiya yordamida coverage va requirement reportlari uchun
  // period array qaytaradi.
  // Joriy kundan boshlab keyingi oy oxirigacha kunlik, keyingi 9 oy oylik chiqadi
  // Minimum 40 hafta qaytadi
  public static function getPeriodDay() {
    $from = date('Y-m-d');
    $to = date('Y-m-t', strtotime('+1 month'));
    $begin = new DateTime($from);
    $end = new DateTime($to);
    $end = $end->modify('+1 day');
    $daterange = new DatePeriod($begin, new DateInterval('P1D'), $end);
    $period = [];
    foreach($daterange as $date) {
      unset($tmpArr);
      $tmpArr['plandate'] = $date->format("Y-m-d");
      $tmpArr['from'] = $date->format("Y-m-d");
      $tmpArr['to'] = $date->format("Y-m-d");
      $period[] = $tmpArr;
    }
    $next2month = date('Y-m-01', strtotime('+2 month'));
    for($i = 0; $i < 9; $i++) {
      unset($tmpArr);
      $tmpArr['plandate'] = date('Y-m', strtotime('+'.$i.' month', strtotime($next2month)));
      $tmpArr['from'] = date('Y-m-d', strtotime('+'.$i.' month', strtotime($next2month)));
      $tmpArr['to'] = date('Y-m-t', strtotime('+'.$i.' month', strtotime($next2month)));
      $period[] = $tmpArr;
    }
    return $period;
  }

  // Ushbu funcksiya DOH ni yillik hisoblash uchun ishlatiladi
  // Joriy kundan boshlab minimum 40 hafta kunlik chiqadi
  public static function getPeriodFull($start_date = null) {
    $from = (empty($start_date)) ? date('Y-m-d') : $start_date;
    $to = date('Y-m-d', strtotime('+10 month', strtotime($from)));
    $begin = new DateTime($from);
    $end = new DateTime($to);
    $end = $end->modify('+1 day');
    $daterange = new DatePeriod($begin, new DateInterval('P1D'), $end);
    $period = [];
    foreach($daterange as $date)
      $period[] = $date->format("Y-m-d");
    return $period;
  }

  // Ushbu funcksiya yordamida in transit reporti uchun
  // period array qaytaradi.
  // Joriy kundan boshlab 2 oy kunlik chiqadi
  public static function getPeriod60Days() {
    $from = date('Y-m-d');
    $to = date('Y-m-d', strtotime('+2 month'));
    $begin = new DateTime($from);
    $end = new DateTime($to);
    $end = $end->modify('+1 day');
    $daterange = new DatePeriod($begin, new DateInterval('P1D'), $end);
    $period = [];
    foreach($daterange as $date) {
      unset($tmpArr);
      $tmpArr['plandate'] = $date->format("Y-m-d");
      $tmpArr['from'] = $date->format("Y-m-d");
      $tmpArr['to'] = $date->format("Y-m-d");
      $period[] = $tmpArr;
    }
    return $period;
  }

  // Ushbu funcksiya DOH reporti uchun
  // kunlik o'rtacha requirementni hisoblashda foydalaniladi (90 kundan o'rtachasi olinadi)
  // Joriy kundan boshlab 3 oy kunlik chiqadi
  public static function getPeriod90Days() {
    $from = date('Y-m-d');
    $to = date('Y-m-d', strtotime('+3 month'));
    $begin = new DateTime($from);
    $end = new DateTime($to);
    $end = $end->modify('+1 day');
    $daterange = new DatePeriod($begin, new DateInterval('P1D'), $end);
    $period = [];
    foreach($daterange as $date) {
      unset($tmpArr);
      $tmpArr['plandate'] = $date->format("Y-m-d");
      $tmpArr['from'] = $date->format("Y-m-d");
      $tmpArr['to'] = $date->format("Y-m-d");
      $period[] = $tmpArr;
    }
    return $period;
  }

  // Ushbu funcksiya yordamida coverage va requirement reportlari uchun
  // period array qaytaradi.
  // Joriy kundan boshlab keyingi oy oxirigacha haftalik (7 kunlik), keyingi 9 oy oylik chiqadi
  // Minimum 40 hafta qaytadi
  public static function getPeriodWeek() {
    $from = date('Y-m-d');
    $to = date('Y-m-t', strtotime('+1 month'));
    $begin = new DateTime($from);
    $end = new DateTime($to);
    $end = $end->modify('+1 day');
    $daterange = new DatePeriod($begin, new DateInterval('P1W'), $end);
    $period = [];
    foreach($daterange as $date) {
      unset($tmpArr);
      $tmpArr['from'] = $date->format("Y-m-d");
      $tmpArr['to'] = $date->modify('+6 day')->format("Y-m-d");
      if($tmpArr['to'] > $to) {
        $tmpArr['to'] = $to;
      }
      $tmpArr['plandate'] = $tmpArr['from'].' - '.$tmpArr['to'];
      $period[] = $tmpArr;
    }
    $date_begin_monthes = new DateTime();
    $date_begin_monthes->modify('first day of second month');
    $next2month = $date_begin_monthes->format('Y-m-d');
    for($i = 0; $i < 9; $i++) {
      unset($tmpArr);
      $tmpArr['from'] = date('Y-m-d', strtotime('+'.$i.' month', strtotime($next2month)));
      $tmpArr['to'] = date('Y-m-t', strtotime('+'.$i.' month', strtotime($next2month)));
      $tmpArr['plandate'] = date('Y-m', strtotime('+'.$i.' month', strtotime($next2month)));
      $period[] = $tmpArr;
    }
    return $period;
  }

  // Ushbu funcksiya yordamida coverage va requirement reportlari uchun
  // period array qaytaradi.
  // Joriy kundan boshlab keyingi oy oxirigacha haftalik (dushunbadan boshlanadi), keyingi 9 oy oylik chiqadi
  // Minimum 40 hafta qaytadi
  public static function getPeriodWeek2($start_date = null) {
    $start_date = (empty($start_date)) ? date('Y-m-d') : $start_date;
    // $to = date('Y-m-d', strtotime('+10 month', strtotime($from)));
    //$from = date('Y-m-d', strtotime('next Monday'));
    $from = date('Y-m-d', strtotime("next Monday", strtotime($start_date)));
    $to = date('Y-m-t', strtotime('+1 month'));
    $begin = new DateTime($from);
    $end = new DateTime($to);
    $end = $end->modify('+1 day');
    $daterange = new DatePeriod($begin, new DateInterval('P1W'), $end);
    $period = [];
    unset($tmpArr);
    // $tmpArr['from'] = date('Y-m-d');
    // $tmpArr['to'] = date('Y-m-d', strtotime('next Sunday'));
    $tmpArr['from'] = date('Y-m-d', strtotime($start_date));
    $tmpArr['to'] = date('Y-m-d', strtotime("next Sunday", strtotime($start_date)));
    $tmpArr['plandate'] = $tmpArr['from'].' - '.$tmpArr['to'];
    $period[] = $tmpArr;
    foreach($daterange as $date) {
      unset($tmpArr);
      $tmpArr['from'] = $date->format("Y-m-d");
      $tmpArr['to'] = $date->modify('+6 day')->format("Y-m-d");
      if($tmpArr['to'] > $to) {
        $tmpArr['to'] = $to;
      }
      $tmpArr['plandate'] = $tmpArr['from'].' - '.$tmpArr['to'];
      $period[] = $tmpArr;
    }
    $date_begin_monthes = new DateTime();
    $date_begin_monthes->modify('first day of second month');
    $next2month = $date_begin_monthes->format('Y-m-d');
    for($i = 0; $i < 9; $i++) {
      unset($tmpArr);
      $tmpArr['from'] = date('Y-m-d', strtotime('+'.$i.' month', strtotime($next2month)));
      $tmpArr['to'] = date('Y-m-t', strtotime('+'.$i.' month', strtotime($next2month)));
      $tmpArr['plandate'] = date('Y-m', strtotime('+'.$i.' month', strtotime($next2month)));
      $period[] = $tmpArr;
    }
    return $period;
  }


    // Ushbu funcksiya yordamida coverage va requirement reportlari uchun
  // period array qaytaradi.
  // Joriy kundan boshlab 6 oy haftalik (dushunbadan boshlanadi), keyingi 5 oy oylik chiqadi
  // Minimum 40 hafta qaytadi
  public static function getPeriodWeek6Month($start_date = null) {

    $start_date = (empty($start_date)) ? date('Y-m-d') : $start_date;
    $from = date('Y-m-d', strtotime("next Monday", strtotime($start_date)));
    $to = date('Y-m-t', strtotime('+5 month'));

    $begin = new DateTime($from);
    $end = new DateTime($to);

    $end = $end->modify('+1 day');
    $daterange = new DatePeriod($begin, new DateInterval('P1W'), $end);

    $period = [];
    unset($tmpArr);
    // $tmpArr['from'] = date('Y-m-d');
    // $tmpArr['to'] = date('Y-m-d', strtotime('next Sunday'));

    $tmpArr['from'] = date('Y-m-d', strtotime($start_date));
    $tmpArr['to'] = date('Y-m-d', strtotime("next Sunday", strtotime($start_date)));
    $tmpArr['plandate'] = $tmpArr['from'].' - '.$tmpArr['to'];
    $period[] = $tmpArr;

    foreach($daterange as $date) {
      unset($tmpArr);
      $tmpArr['from'] = $date->format("Y-m-d");
      $tmpArr['to'] = $date->modify('+6 day')->format("Y-m-d");
      if($tmpArr['to'] > $to) {
        $tmpArr['to'] = $to;
      }
      $tmpArr['plandate'] = $tmpArr['from'].' - '.$tmpArr['to'];
      $period[] = $tmpArr;
    }

    $date_begin_monthes = new DateTime();
    $date_begin_monthes->modify('first day of sixth month');
    $next4month = $date_begin_monthes->format('Y-m-d');

    for($i = 0; $i < 5; $i++) {
      unset($tmpArr);
      $tmpArr['from'] = date('Y-m-d', strtotime('+'.$i.' month', strtotime($next4month)));
      $tmpArr['to'] = date('Y-m-t', strtotime('+'.$i.' month', strtotime($next4month)));
      $tmpArr['plandate'] = date('Y-m', strtotime('+'.$i.' month', strtotime($next4month)));
      $period[] = $tmpArr;
    }

    return $period;
  }

  /**
   * Function that groups an array of associative arrays by some key.
   *
   * @param {String} $key Property to sort by.
   * @param {Array} $data Array that stores multiple associative arrays.
   *
   * @return array
   */
  public static function groupbyKeyFromArray($key, $data) {
    $result = [];
    foreach($data as $val) {
      if(array_key_exists($key, $val)) {
        $result[$val[$key]][] = $val;
      } else {
        $result[""][] = $val;
      }
    }
    return $result;
  }

  /**
   * Groups an array by a given key.
   * Groups an array into arrays by a given key, or set of keys, shared between all array members.
   * Based on {@author Jake Zatecky}'s {@link https://github.com/jakezatecky/array_group_by array_group_by()} function.
   * This variant allows $key to be closures.
   *
   * @param array $array   The array to have grouping performed on.
   * @param mixed $key,... The key to group or split by. Can be a _string_,
   *                       an _integer_, a _float_, or a _callable_.
   *                       If the key is a callback, it must return
   *                       a valid key from the array.
   *                       If the key is _NULL_, the iterated element is skipped.
   *                       ```
   *                       string|int callback ( mixed $item )
   *                       ```
   *
   * @return array|null Returns a multidimensional array or `null` if `$key` is invalid.
   */
  public static function groupArrayByField(array $array, $key) {
    if(!is_string($key) && !is_int($key) && !is_float($key) && !is_callable($key)) {
      trigger_error('array_group_by(): The key should be a string, an integer, or a callback', E_USER_ERROR);
      return null;
    }
    $func = (!is_string($key) && is_callable($key) ? $key : null);
    $_key = $key;
    // Load the new array, splitting by the target key
    $grouped = [];
    foreach($array as $value) {
      $key = null;
      if(is_callable($func)) {
        $key = call_user_func($func, $value);
      } elseif(is_object($value) && property_exists($value, $_key)) {
        $key = $value->{$_key};
      } elseif(isset($value[$_key])) {
        $key = $value[$_key];
      }
      if($key === null) {
        continue;
      }
      $grouped[$key][] = $value;
    }
    // Recursively build a nested grouping if more parameters are supplied
    // Each grouped array value is grouped according to the next sequential key
    if(func_num_args() > 2) {
      $args = func_get_args();
      foreach($grouped as $key => $value) {
        $params = array_merge([$value], array_slice($args, 2, func_num_args()));
        $grouped[$key] = call_user_func_array('self::groupArrayByField', $params);
      }
    }
    return $grouped;
  }

  /**
   * Array(massiv) ni string ga o`zgartirish
   *
   * @param array  $arrayVar  string ga aylantirilishi kerak bo`lgan array(massiv).
   * @param string $separator arrayning index(key)i bilan ajratib turuvchi string odatda ' '(bo`sh joy),<br>     *
   *                          Masalan:<br>
   *                          arrayToStringRecursive($array,' ');<br>
   *                          yoki<br>
   *                          arrayToStringRecursive($model->errors,' > ');<br>
   *
   * @return string|null Natija string qaytadi.
   **/
  public static function arrayToStringRecursive(array $arrayVar = [], string $separator = ' ') {
    $output = "";
    foreach($arrayVar as $key => $av) {
      if(is_array($av)) {
        $output .= "<br><strong>".$key.": </strong>".self::arrayToStringRecursive($av, $separator);
      } else {
        $output .= $separator.$av;
      }
    }
    return $output;
  }

  public static function formatRemoveDecimal($number, $dec = 2) {
    if(!empty($number)) {
      if(($number - floor($number)) == 0) {
        return number_format($number, 0, '', '');
      } else {
        return number_format($number, $dec, '.', '');
      }
    }
    return 0;
  }

  public static function numberFormatRemoveZero($number = 0, int $decimal = 2, string $dec_point = '.', string $thousands_sep = " ", bool $remove_zero = true, bool $show_zero = true) {
//    $number=42350.25;

    $formated_number = 0;
    if(empty($number) || $number == null) {
      $number = 0;
    }
    if(!empty($number) || ($number) != null || $number != 0) {
      if(intval($number) == $number) {
        $formated_number = number_format($number, $decimal, $dec_point, $thousands_sep);
      } else {
        $formated_number = number_format($number, $decimal, $dec_point, $thousands_sep);
      }
    }
    $decVal = $number - (int)$number;
    if(($decVal == 0)) {
      $formated_number = number_format($number, 0, $dec_point, $thousands_sep);
    } elseif(($remove_zero == true) && ($decimal > 0)) {
      $formated_number = rtrim($formated_number, 0);
      $formated_number = rtrim($formated_number, '.');
    }
    if($show_zero == true && $number == 0) {
      $formated_number = 0;
    }
    if($show_zero == false && $number == 0) {
      $formated_number = '';
    }
    return $formated_number;
  }

  /**
   * Raqamni chiroyli ko`rinishda formatlash.
   *
   * @param float    $number     Formatlanishi kerak bo`lgan(Kiritilgan son-"KS") son. Odatda 0,<br>
   * @param int    $decimal      Formatlangan sonning butun qismi. Odatda 2 xona,<br>
   * @param string $decPoint     Sonning butun va kasr qismini ajratib turuvchi belgi. Odatda '.'(nuqta),<br>
   * @param string $thousandsSep Minglikni ajratuvchi belgi. Odatda ' '(bo`sh joy),<br>
   * @param bool   $removeZero   0(Nol)larni o`chirish. Odatda true,<br>
   * @param bool   $showZero     0(Nol)larni ko`rsatish. Odatda true,<br>
   *                             Masalan:<br>
   *                             $fromatedNumber = UIHelper::numberFormat(123456.0098000);<br>
   *                             Natija: $fromatedNumber = 123 456.01;<br>
   *                             $fromatedNumber = UIHelper::numberFormat(123456.0098000,3,'.','`',false);<br>
   *                             Natija: $fromatedNumber = 123`456.010;<br>
   *                             $fromatedNumber = UIHelper::numberFormat(123456.0098000,3,'.','`',true,false);<br>
   *                             Natija: $fromatedNumber = 123`456.01;<br>
   *
   * @return string|null Natija string qaytadi.
   **/
  public static function numberFormat($number = 0, int $decimal = 2, string $decPoint = '.', string $thousandsSep = " ", bool $removeZero = true, bool $showZero = false) {
    if(($number === 0 || empty($number) || $number === null)) {
      if($showZero === true) {
        return 0;
      }else{
        return '';
      }
    }
    $formatedNumber = null;
    if(intval($number) === $number) {
      $formatedNumber = number_format($number, $decimal, $decPoint, $thousandsSep);
    } else {
      $formatedNumber = number_format($number, $decimal, $decPoint, $thousandsSep);
    }
    $decVal = $number - (int)$number;
    if(($decVal == 0)) {
      $formatedNumber = number_format((float)$number, 0, $decPoint, $thousandsSep);
    } elseif(($removeZero == true) && ($decimal > 0)) {
      $formatedNumber = rtrim($formatedNumber, 0);
      $formatedNumber = rtrim($formatedNumber, $decPoint);
    }
    return $formatedNumber;
  }

  public static function generateQrcode(string $value = '') {
    $qrCode = (new QrCode($value))
      ->setErrorCorrectionLevel(ErrorCorrectionLevelInterface::HIGH);
    $pngData = $qrCode->writeString();
    return base64_encode(($pngData));
  }


  //--------------------------------------------------------
  // Функция для преобразования числа в сумму прописью
  // @author runcore
  // @url rche.ru
  //--------------------------------------------------------
  public static function sum2str_ru($summa, $stripkop = false) {
    $nol = 'ноль';
    $str[100] = ['', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот'];
    $str[11] = ['', 'десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать', 'двадцать'];
    $str[10] = ['', 'десять', 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто'];
    $sex = [
      ['', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'],// m
      ['', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'] // f
    ];
    $forms = [
      ['тийин', 'тийин', 'тийин', 1],             // 10^-2
      ['сум', 'сума', 'сума', 0],                 // 10^ 0
      ['тысяча', 'тысячи', 'тысяч', 1],           // 10^ 3
      ['миллион', 'миллиона', 'миллионов', 0],    // 10^ 6
      ['миллиард', 'миллиарда', 'миллиардов', 0], // 10^ 9
      ['триллион', 'триллиона', 'триллионов', 0], // 10^12
    ];
    $out = $tmp = [];
    // Поехали!
    $tmp = explode('.', str_replace(',', '.', $summa));
    $rub = number_format($tmp[0], 0, '', '-');
    if($rub == 0)
      $out[] = $nol;
    // нормализация копеек
    $kop = isset($tmp[1]) ? substr(str_pad($tmp[1], 2, '0', STR_PAD_RIGHT), 0, 2) : '00';
    $segments = explode('-', $rub);
    $offset = sizeof($segments);
    if((int)$rub == 0) { // если 0 рублей
      $o[] = $nol;
      $o[] = self::morph(0, $forms[1][0], $forms[1][1], $forms[1][2]);
    } else {
      foreach($segments as $k => $lev) {
        $sexi = (int)$forms[$offset][3]; // определяем род
        $ri = (int)$lev;                 // текущий сегмент
        if($ri == 0 && $offset > 1) {// если сегмент==0 & не последний уровень(там Units)
          $offset--;
          continue;
        }
        // нормализация
        $ri = str_pad($ri, 3, '0', STR_PAD_LEFT);
        // получаем циферки для анализа
        $r1 = (int)substr($ri, 0, 1);    //первая цифра
        $r2 = (int)substr($ri, 1, 1);    //вторая
        $r3 = (int)substr($ri, 2, 1);    //третья
        $r22 = (int)$r2.$r3;             //вторая и третья
        // разгребаем порядки
        if($ri > 99)
          $o[] = $str[100][$r1];         // Сотни
        if($r22 > 20) {// >20
          $o[] = $str[10][$r2];
          $o[] = $sex[$sexi][$r3];
        } else { // <=20
          if($r22 > 9)
            $o[] = $str[11][$r22 - 9]; // 10-20
          elseif($r22 > 0)
            $o[] = $sex[$sexi][$r3]; // 1-9
        }
        // Рубли
        $o[] = self::morph($ri, $forms[$offset][0], $forms[$offset][1], $forms[$offset][2]);
        $offset--;
      }
    }
    // Копейки
    if(!$stripkop) {
      $o[] = $kop;
      $o[] = self::morph($kop, $forms[0][0], $forms[0][1], $forms[0][2]);
    }
    return preg_replace("/\s{2,}/", ' ', implode(' ', $o));
  }

  // --------------------------------------------------------
  // Склоняем словоформу
  // --------------------------------------------------------
  public static function morph($n, $f1, $f2, $f5) {
    $n = abs($n)%100;
    $n1 = $n%10;
    if($n > 10 && $n < 20)
      return $f5;
    if($n1 > 1 && $n1 < 5)
      return $f2;
    if($n1 == 1)
      return $f1;
    return $f5;
  }

  public static function mb_ucfirst($str, $encoding = "UTF-8", $lower_str_end = false) {
    $first_letter = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding);
    $str_end = "";
    if($lower_str_end) {
      $str_end = mb_strtolower(mb_substr($str, 1, mb_strlen($str, $encoding), $encoding), $encoding);
    } else {
      $str_end = mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
    }
    $str = $first_letter.$str_end;
    return $str;
  }

  public static function random_color_part() {
    return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
  }

  public static function random_color() {
    return '#'.self::random_color_part().self::random_color_part().self::random_color_part();
  }

  public static function dif_balance_sts($plan = 0, $fakt = 0) {
    $sts = 0; // = "-1"-minus; "0"-OK; "1"-plus;
    if($plan > 0) {
      $deviation = Yii::$app->params['deviation']; // 20
      $balance = 100 - ($fakt/$plan*100);          // 100-(100/124*100)=-24;
      if($balance >= -($deviation) && $balance <= $deviation) {
        $sts = 0;
      } elseif($balance < -($deviation)) {
        $sts = 1;
      } elseif($balance > $deviation) {
        $sts = -1;
      }
    } else {
      $sts = 1;
    }
    return $sts;
  }

  public static function downloadFileName($fileName, $extension = 'xlsx') {
    $prefix = Yii::$app->params['comp_short_name'] ?? 'xx';
    return implode('_', [$prefix, $fileName, date('Y-m-d_H_i')]).'.'.$extension;
  }

  public static function currencyRates($date = null) {
    return [
      'rateUSD' => CurrencyRate::currentRate(Currency::findOneCurrencyCode('USD')->id, $date),
      'rateEUR' => CurrencyRate::currentRate(Currency::findOneCurrencyCode('EUR')->id, $date),
      'rateRUB' => CurrencyRate::currentRate(Currency::findOneCurrencyCode('RUB')->id, $date),
      'rateUZS' => 1
    ];
  }

  //////////////////////////////////////////////////////////////////////
  //PARA: Date Should In YYYY-MM-DD Format
  //RESULT FORMAT:
  // '%y Year %m Month %d Day %h Hours %i Minute %s Seconds'        =>  1 Year 3 Month 14 Day 11 Hours 49 Minute 36 Seconds
  // '%y Year %m Month %d Day'                                    =>  1 Year 3 Month 14 Days
  // '%m Month %d Day'                                            =>  3 Month 14 Day
  // '%d Day %h Hours'                                            =>  14 Day 11 Hours
  // '%d Day'                                                        =>  14 Days
  // '%h Hours %i Minute %s Seconds'                                =>  11 Hours 49 Minute 36 Seconds
  // '%i Minute %s Seconds'                                        =>  49 Minute 36 Seconds
  // '%h Hours                                                    =>  11 Hours
  // '%a Days                                                        =>  468 Days
  //////////////////////////////////////////////////////////////////////
  public static function dateDifference($date_1 , $date_2 , $differenceFormat = '%a' )
  {
      $datetime1 = date_create($date_1);
      $datetime2 = date_create($date_2);

      $interval = date_diff($datetime1, $datetime2);

      return $interval->format($differenceFormat);

  }

  public static function convertCurrency($value, $fromCurrency, $toCurrency, $date = null){
    if(empty($fromCurrency) or empty($toCurrency)) return $value;
    extract(Helpers::currencyRates($date));
    return $value / ${'rate'.$toCurrency} * ${'rate'.$fromCurrency};

  }

}
