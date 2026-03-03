<?php
/**
 * This is the shortcut to DIRECTORY_SEPARATOR
 */
defined('DS') or define('DS',DIRECTORY_SEPARATOR);





function divideString($str, $n) {
    return strrev(chunk_split (strrev($str), $n,' '));
}

function vd($var, $exit = true)
{
    $dumper = new yii\helpers\BaseVarDumper();
    echo $dumper::dump($var, 10, true);
    if ($exit)
        exit;
}
function sendMessage($chatID, $messaggio) {
    $token = '5876532002:AAHqLXOdEojQ5-UF277SSR7ix-rYFfhYqzU';
    echo "sending message to " . $chatID . "\n";

    $url = "https://api.telegram.org/bot" . $token . "/sendMessage?chat_id=" . $chatID;
    $url = $url . "&text=".$messaggio;
    $ch = curl_init();
    $optArray = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true
    );
    curl_setopt_array($ch, $optArray);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
?>