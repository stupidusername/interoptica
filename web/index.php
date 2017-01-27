<?php

// Set config based on the environment
// Use production as default
$env = getenv('APP_ENV') ? getenv('APP_ENV') : 'prod';
defined('YII_ENV') or define('YII_ENV', $env);
if ($env == 'dev') {
	defined('YII_DEBUG') or define('YII_DEBUG', true);
}

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../config/web.php');

(new yii\web\Application($config))->run();
