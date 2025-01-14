<?php

// NOTE: Make sure this file is not accessible when deployed to production
if (!in_array(@$_SERVER['REMOTE_ADDR'], ['*'])) {
    die('You are not allowed to access this file.');
}

// Bootstraping tests environment
require __DIR__.'/../../tests/bootstrap.php';

// TEST ENV
defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'test');
defined('YII_APP_BASE_PATH') or define('YII_APP_BASE_PATH', dirname(dirname(__DIR__)));

// Environment
require __DIR__.'/../../common/env.php';

// Yii
require __DIR__.'/../../vendor/yiisoft/yii2/Yii.php';

// Bootstrap application
require __DIR__.'/../../common/config/bootstrap.php';
require __DIR__.'/../config/bootstrap.php';

$config = require __DIR__.'/../../tests/codeception/config/backend/acceptance.php';

(new \common\components\location\Application($config))->run();
