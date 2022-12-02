<?php


namespace app\services;


use app\library\telegram\Telegram;

class TelegramService
{

  public static function sendToGroup($data)
  {
    $token = '5871348680:AAHPRi8zEE0w1MuriilwDDtF5yrwMypRvV8';
    $chat_id = -610217454;

    $telegram = new Telegram($token);
    $text = '#new'. PHP_EOL;
    $text .= '<b>ID: '.$data['id'].'</b>'. PHP_EOL;
    $text .= '--------------------------'.PHP_EOL;
    $text .='<b>Дата: </b>'.$data['date'].PHP_EOL;
    $text .= '--------------------------'.PHP_EOL;
    $text .='<b>Материал: </b>'.$data['material'].PHP_EOL;
    $text .= '--------------------------'.PHP_EOL;
    $text .='<b>Вес: </b>'.$data['weight'].PHP_EOL;
    $text .= '--------------------------'.PHP_EOL;
    $text .='<b>Откуда: </b>'.$data['is_where'];

    $content = array(
      'chat_id' => $chat_id,
      'text' => $text,
      'parse_mode' => 'HTML'
    );
    //dd($content);
    $telegram->sendMessage($content);
  }

  public static function productionOrder($data)
  {
    $token = '5871348680:AAHPRi8zEE0w1MuriilwDDtF5yrwMypRvV8';
    $chat_id = -610217454;

    $telegram = new Telegram($token);
    $text = '#new'. PHP_EOL;
    $text .= '<b>Floc: '.$data['floc'].'</b>'. PHP_EOL;
    // $text .= '<b>Модель: '.$data['model'].'</b>'. PHP_EOL;
    $text .= '--------------------------'.PHP_EOL;
    $text .='<b>Side: </b>'.$data['side'].PHP_EOL;
    $text .= '--------------------------'.PHP_EOL;
    $text .='<b>Готовый продукт: </b>'.$data['code'].PHP_EOL;
    $text .= '--------------------------'.PHP_EOL;
    $text .='<b>Количество: </b>'.$data['quantity'].PHP_EOL;
    $text .= '--------------------------'.PHP_EOL;
    $text .='<b>Копии: </b>'.$data['quantity_of_copy'];

    $content = array(
      'chat_id' => $chat_id,
      'text' => $text,
      'parse_mode' => 'HTML'
    );
    //dd($content);
    $telegram->sendMessage($content);
  }

  public static function productionPlan($data)
  {
    $token = '5871348680:AAHPRi8zEE0w1MuriilwDDtF5yrwMypRvV8';
    $chat_id = -610217454;

    $telegram = new Telegram($token);
    $text = '#new'. PHP_EOL;
    $text .= '<b>№ материал: '.$data['part_id'].'</b>'. PHP_EOL;
    // $text .= '<b>Модель: '.$data['model'].'</b>'. PHP_EOL;
    $text .= '--------------------------'.PHP_EOL;
    $text .='<b>Расположение: </b>'.$data['warehouse_id'].PHP_EOL;
    $text .= '--------------------------'.PHP_EOL;
    $text .='<b>Дата производство: </b>'.$data['production_date'].PHP_EOL;
    $text .= '--------------------------'.PHP_EOL;
    $text .='<b>Смена: </b>'.$data['shift'].PHP_EOL;
    $text .= '--------------------------'.PHP_EOL;
    $text .='<b>Кол.план: </b>'.$data['target_qty'];

    $content = array(
      'chat_id' => $chat_id,
      'text' => $text,
      'parse_mode' => 'HTML'
    );
    //dd($content);
    $telegram->sendMessage($content);
  }
}