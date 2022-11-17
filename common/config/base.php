<?php
$config = [
    'name' => 'Arcadia Academy of Music',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'extensions' => require(__DIR__ . '/../../vendor/yiisoft/extensions.php'),
    'bootstrap' => ['log', 'rollbar', 'queue'],
    'timeZone' => 'US/Eastern',
    'sourceLanguage' => 'en-US',
    'language' => 'en-US',
    'components' => [

        'queue' => [
            'class' => \yii\queue\db\Queue::class,
            'db' => 'db', // DB connection component or its config 
            'tableName' => '{{%queue}}', // Table name
            'channel' => 'default', // Queue channel key
            'mutex' => \yii\mutex\MysqlMutex::class, // Mutex used to sync queries
        ],

        'authManager' => [
            'class' => 'common\components\rbac\DbManager',
            'itemTable' => '{{%rbac_auth_item}}',
            'itemChildTable' => '{{%rbac_auth_item_child}}',
            'assignmentTable' => '{{%rbac_auth_assignment}}',
            'ruleTable' => '{{%rbac_auth_rule}}',
        ],

        'cache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@common/runtime/cache',
        ],

        'commandBus' => [
            'class' => 'trntv\bus\CommandBus',
            'middlewares' => [
                [
                    'class' => '\trntv\bus\middlewares\BackgroundCommandMiddleware',
                    'backgroundHandlerPath' => '@console/yii',
                    'backgroundHandlerRoute' => 'command-bus/handle',
                ],
            ],
        ],

        'formatter' => [
            'class' => 'common\components\formatter\CustomFormatter',
            'dateFormat' => 'php:M d, Y',
            'datetimeFormat' => 'php:M d, Y g:i a',
            'timeFormat' => 'php:h:i a',
            'currencyCode' => 'USD',
            'nullDisplay' => '',
            'thousandSeparator' => ',',
            'timeZone' => 'US/Eastern',
            'defaultTimeZone' => 'US/Eastern',
            'numberFormatterOptions' => [
                //    NumberFormatter::MIN_FRACTION_DIGITS => 2,
                //    NumberFormatter::MAX_FRACTION_DIGITS => 2,
            ]
        ],

        'glide' => [
            'class' => 'trntv\glide\components\Glide',
            'sourcePath' => '@storage/web/source',
            'cachePath' => '@storage/cache',
            'urlManager' => 'urlManagerStorage',
            'maxImageSize' => env('GLIDE_MAX_IMAGE_SIZE'),
            'signKey' => env('GLIDE_SIGN_KEY'),
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => env('SMTP_HOST'),
                'username' => env('SMTP_USERNAME'),
                'password' => env('SMTP_PASSWORD'),
                'port' => env('SMTP_PORT'),
                'encryption' => env('SMTP_ENCRYPTION'),
                'streamOptions' => [
                    'ssl' => [
                        // 'allow_self_signed' => true,
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ],
            ],
            'as catchAllEmail' => [
                'class' => '\common\behaviors\CatchAllEmailBehavior',
            ],
        ],

        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => env('DB_DSN'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'tablePrefix' => env('DB_TABLE_PREFIX'),
            'charset' => 'utf8',
            'enableSchemaCache' => YII_ENV_PROD,
        ],

        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                'db' => [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error', 'warning'],
                    'except' => ['yii\web\HttpException:*', 'yii\i18n\I18N\*'],
                    'prefix' => function () {
                        $url = !Yii::$app->request->isConsoleRequest ? Yii::$app->request->getUrl() : null;

                        return sprintf('[%s][%s]', Yii::$app->id, $url);
                    },
                    'logVars' => [],
                    'logTable' => '{{%system_log}}',
                ],
                'rollbar' =>  [
                    'class' => 'baibaratsky\yii\rollbar\log\Target',
                    'levels' => ['error'], // Log levels you want to appear in Rollbar

                    'categories' => ['application'],
                ],
            ],
        ],

        'i18n' => [
            'translations' => [
                'app' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                ],
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                    'fileMap' => [
                        'common' => 'common.php',
                        'backend' => 'backend.php',
                        'frontend' => 'frontend.php',
                    ],
                    'on missingTranslation' => ['\backend\modules\i18n\Module', 'missingTranslation'],
                ],
                /* Uncomment this code to use DbMessageSource
                 '*'=> [
                    'class' => 'yii\i18n\DbMessageSource',
                    'sourceMessageTable'=>'{{%i18n_source_message}}',
                    'messageTable'=>'{{%i18n_message}}',
                    'enableCaching' => YII_ENV_DEV,
                    'cachingDuration' => 3600,
                    'on missingTranslation' => ['\backend\modules\i18n\Module', 'missingTranslation']
                ],
                */
            ],
        ],

        'fileStorage' => [
            'class' => '\trntv\filekit\Storage',
            'baseUrl' => '@storageUrl/source',
            'filesystem' => [
                'class' => 'common\components\filesystem\LocalFlysystemBuilder',
                'path' => '@storage/web/source',
            ],
            'as log' => [
                'class' => 'common\behaviors\FileStorageLogBehavior',
                'component' => 'fileStorage',
            ],
        ],

        'keyStorage' => [
            'class' => 'common\components\keyStorage\KeyStorage',
        ],

        'urlManagerBackend' => \yii\helpers\ArrayHelper::merge(
            [
                'hostInfo' => Yii::getAlias('@backendUrl'),
            ],
            require(Yii::getAlias('@backend/config/_urlManager.php'))
        ),
        'urlManagerFrontend' => \yii\helpers\ArrayHelper::merge(
            [
                'hostInfo' => Yii::getAlias('@frontendUrl'),
            ],
            require(Yii::getAlias('@frontend/config/_urlManager.php'))
        ),
        'urlManagerStorage' => \yii\helpers\ArrayHelper::merge(
            [
                'hostInfo' => Yii::getAlias('@storageUrl'),
            ],
            require(Yii::getAlias('@storage/config/_urlManager.php'))
        ),
        'rollbar' => [
            'class' => 'baibaratsky\yii\rollbar\Rollbar',
            'accessToken' => env('ROLLBAR_POST_SERVER_ITEM'),
            'environment' => env('ROLLBAR_ENVIRONMENT'),
        ],
    ],
    'params' => [
        'adminEmail' => env('ADMIN_EMAIL'),
        'robotEmail' => env('ROBOT_EMAIL'),
        'availableLocales' => [
            'en-US' => 'English (US)',
            'ru-RU' => 'Русский (РФ)',
            'uk-UA' => 'Українська (Україна)',
            'es' => 'Español',
            'zh-CN' => '简体中文',
        ],
    ],
    'container' => [
        'definitions' => [
            'yii\widgets\LinkPager' => [
                'firstPageLabel' => 'First',
                'lastPageLabel'  => 'Last',
            ]
        ]
    ]
];

if (YII_ENV_PROD) {
    $config['components']['log']['targets']['email'] = [
        'class' => 'yii\log\EmailTarget',
        'except' => ['yii\web\HttpException:*'],
        'levels' => ['error', 'warning'],
        'message' => ['from' => env('ROBOT_EMAIL'), 'to' => env('ADMIN_EMAIL')],
    ];
}

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];

    $config['components']['cache'] = [
        'class' => 'yii\caching\DummyCache',
    ];
}

return $config;
