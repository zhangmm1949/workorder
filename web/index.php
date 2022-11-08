<?php

$local_config = require __DIR__ . '/../config/local_config.php';

// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', $local_config['YII_DEBUG']);
defined('YII_ENV') or define('YII_ENV', $local_config['YII_ENV']);

error_reporting(E_ALL^E_WARNING^E_NOTICE);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';
require(__DIR__ . '/../functions.php');

$config = require __DIR__ . '/../config/web.php';

(new yii\web\Application($config))->run();
