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
}