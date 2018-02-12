<?php

$config = [
    'homeUrl' => Yii::getAlias('@backendUrl'),
    'controllerNamespace' => 'backend\controllers',
    'defaultRoute' => '/dashboard/index',
    'controllerMap' => [
        'file-manager-elfinder' => [
            'class' => 'mihaildev\elfinder\Controller',
            'access' => ['manager'],
            'disabledCommands' => ['netmount'],
            'roots' => [
                [
                    'baseUrl' => '@storageUrl',
                    'basePath' => '@storage',
                    'path' => '/',
                    'access' => ['read' => 'manager', 'write' => 'manager'],
                ],
            ],
        ],
    ],
    'components' => [
        'session' => [
            'name' => 'PHPBACKSESSID',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'request' => [
            'class' => '\common\components\location\Request',
            'cookieValidationKey' => env('BACKEND_COOKIE_VALIDATION_KEY'),
            'baseUrl' => '/admin',
        ],
        'urlManager' => [
            'class' => '\common\components\location\UrlManager',
            'on locationChanged' => '\common\components\location\LocationChangedEvent::onLocationChanged',
            'enableDefaultLocationUrlCode' => true,
            'ignoreLocationUrlPatterns' => [
                '#^sign-in/(login|logout|request-password-reset)#' => '#^(sign-in|login)#',
            ],
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
        'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'common\models\User',
            'loginUrl' => ['sign-in/login'],
            'enableAutoLogin' => true,
            'as afterLogin' => 'common\behaviors\LoginTimestampBehavior',
        ],
    ],
    'modules' => [
        'i18n' => [
            'class' => 'backend\modules\i18n\Module',
            'defaultRoute' => 'i18n-message/index',
        ],
        'admin' => [
            'class' => 'mdm\admin\Module',
            'controllerMap' => [
                 'assignment' => [
                    'class' => 'mdm\admin\controllers\AssignmentController',
                     'userClassName' => 'common\models\User',
                    'idField' => 'id',
                    'usernameField' => 'email',
                    'fullnameField' => 'publicIdentity',
                    ],
                    'searchClass' => 'backend\models\search\UserSearch',
                ],
        ],
        'gridview' => [
            'class' => '\kartik\grid\Module',
        ],
        'datecontrol' => [
            'class' => '\kartik\datecontrol\Module',
        ],
    ],
    'as beforeAction' => 'common\behaviors\GlobalBeforeAction'
];

if (YII_ENV_DEV) {
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'generators' => [
            'crud' => [
                'class' => 'yii\gii\generators\crud\Generator',
                'templates' => [
                    'yii2-starter-kit' => Yii::getAlias('@backend/views/_gii/templates'),
                ],
                'template' => 'yii2-starter-kit',
                'messageCategory' => 'backend',
            ],
        ],
    ];
}

return $config;
