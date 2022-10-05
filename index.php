<?php

ini_set('max_execution_time', 1200);
ini_set('memory_limit', '1G');


include 'env.php';

require(__DIR__.'/_protected/vendor/autoload.php');
require(__DIR__.'/_protected/vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__.'/_protected/config/web.php');

(new yii\web\Application($config))->run();
