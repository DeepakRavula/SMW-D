<?php

$config = [
    'homeUrl' => Yii::getAlias('@frontendUrl'),
    'controllerNamespace' => 'frontend\controllers',
    'defaultRoute' => 'schedule/index',
    'bootstrap' => ['maintenance'],
    'modules' => [
        'user' => [
            'class' => 'frontend\modules\user\Module',
            //'shouldBeActivated' => true
        ],
        'api' => [
            'class' => 'frontend\modules\api\Module',
            'modules' => [
                'v1' => 'frontend\modules\api\v1\Module',
            ],
        ],
        'gridview' => [
            'class' => '\kartik\grid\Module', 
        ],
    ],
    'components' => [
        'session' => [
            'name' => 'PHPFRONTSESSID',
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'github' => [
                    'class' => 'yii\authclient\clients\GitHub',
                    'clientId' => env('GITHUB_CLIENT_ID'),
                    'clientSecret' => env('GITHUB_CLIENT_SECRET'),
                ],
                'facebook' => [
                    'class' => 'yii\authclient\clients\Facebook',
                    'clientId' => env('FACEBOOK_CLIENT_ID'),
                    'clientSecret' => env('FACEBOOK_CLIENT_SECRET'),
                    'scope' => 'email,public_profile',
                    'attributeNames' => [
                        'name',
                        'email',
                        'first_name',
                        'last_name',
                    ],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'maintenance' => [
            'class' => 'common\components\maintenance\Maintenance',
            'enabled' => function ($app) {
                return $app->keyStorage->get('frontend.maintenance') === 'enabled';
            },
        ],
        'request' => [
            'class' => '\common\components\location\Request',
            'cookieValidationKey' => env('FRONTEND_COOKIE_VALIDATION_KEY'),
            'baseUrl' => '',
        ],
        'urlManager' => [
            'class' => '\common\components\location\UrlManager',
            'on locationChanged' => '\common\components\location\LocationChangedEventFrontend::onLocationChanged',
            'enableDefaultLocationUrlCode' => true,
            'ignoreLocationUrlPatterns' => [
                '#^user/sign-in#' => '#^user/sign-in#',
            ],
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
        'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'common\models\User',
            'loginUrl' => ['/user/sign-in/login'],
            'enableAutoLogin' => true,
            'as afterLogin' => 'common\behaviors\LoginTimestampBehavior',
        ],
    ],
        'as globalAccess' => [
        'class' => '\common\behaviors\GlobalAccessBehavior',
        'rules' => [
            [
                'controllers' => ['user/sign-in'],
                'allow' => true,
                'roles' => ['?', '@'],
                'actions' => ['login', 'request-password-reset', 'reset-password'],
            ],
            [
                'controllers' => ['debug/default'],
                'allow' => true,
            ],
            [
                'controllers' => ['user/sign-in'],
                'allow' => true,
                'roles' => ['@'],
                'actions' => ['logout', 'profile', 'account'],
            ],
            [
                'controllers' => ['user/default'],
                'allow' => true,
                'roles' => ['@'],
            ],
            [
                'controllers' => ['site'],
                'allow' => true,
                'roles' => ['?', '@'],
                'actions' => ['error'],
            ],
            [
                'controllers' => ['site'],
                'allow' => true,
                'roles' => ['@'],
                'actions' => ['index'],
            ],
            [
                'controllers' => ['schedule', 'user/user-contact'],
                'allow' => true,
                'roles' => ['teacher', 'customer'],
            ],
            [
                'controllers' => ['daily-schedule'],
                'allow' => true,
                'roles' => ['teacher', 'customer','staffmember'],
            ],
        ],
    ],
];

if (YII_ENV_DEV) {
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'generators' => [
            'crud' => [
                'class' => 'yii\gii\generators\crud\Generator',
                'messageCategory' => 'frontend',
            ],
        ],
    ];
}

return $config;
