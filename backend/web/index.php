<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');
set_time_limit(180);
// Composer
require __DIR__.'/../../vendor/autoload.php';

// Environment
require __DIR__.'/../../common/env.php';

// Yii
require __DIR__.'/../../vendor/yiisoft/yii2/Yii.php';

// Bootstrap application
require __DIR__.'/../../common/config/bootstrap.php';
require __DIR__.'/../config/bootstrap.php';

$config = \yii\helpers\ArrayHelper::merge(
    require(__DIR__.'/../../common/config/base.php'),
    require(__DIR__.'/../../common/config/web.php'),
    require(__DIR__.'/../config/base.php'),
    require(__DIR__.'/../config/web.php')
);

(new \common\components\location\Application($config))->run();
