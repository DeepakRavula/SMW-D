<?php


$config = [
    'homeUrl'=>Yii::getAlias('@backendUrl'),
    'controllerNamespace' => 'backend\controllers',
    'defaultRoute'=>'timeline-event/index',
    'controllerMap'=>[
        'file-manager-elfinder' => [
            'class' => 'mihaildev\elfinder\Controller',
            'access' => ['manager'],
            'disabledCommands' => ['netmount'],
            'roots' => [
                [
                    'baseUrl' => '@storageUrl',
                    'basePath' => '@storage',
                    'path'   => '/',
                    'access' => ['read' => 'manager', 'write' => 'manager']
                ]
            ]
        ]
    ],
    'components'=>[
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'request' => [
            'cookieValidationKey' => env('BACKEND_COOKIE_VALIDATION_KEY')
        ],
        'user' => [
            'class'=>'yii\web\User',
            'identityClass' => 'common\models\User',
            'loginUrl'=>['sign-in/login'],
            'enableAutoLogin' => true,
            'as afterLogin' => 'common\behaviors\LoginTimestampBehavior'
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',
                'username' => 'sriramjano@gmail.com',
                'password' => 'poonpani',
                'port' => '587',
                'encryption' => 'tls',
            ],
        ],
    ],
    'modules'=>[
        'i18n' => [
            'class' => 'backend\modules\i18n\Module',
            'defaultRoute'=>'i18n-message/index'
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
                    'searchClass' => 'backend\models\search\UserSearch'
                ],
        ],
    ],
    'as globalAccess'=>[
        'class'=>'\common\behaviors\GlobalAccessBehavior',
        'rules'=>[
            [
                'controllers'=>['sign-in'],
                'allow' => true,
                'roles' => ['?'],
                'actions'=>['login','requestpasswordreset','resetpassword']
            ],
            [
                'controllers'=>['sign-in'],
                'allow' => true,
                'roles' => ['@'],
                'actions'=>['logout']
            ],
            [
                'controllers'=>['site'],
                'allow' => true,
                'roles' => ['?', '@'],
                'actions'=>['error']
            ],
            [
                'controllers'=>['debug/default'],
                'allow' => true,
                'roles' => ['?'],
            ],
            [
                'controllers'=>['user'],
                'allow' => true,
                'roles' => ['administrator', 'staff'],
            ],
			[
                'controllers'=>['schedule','student', 'lesson', 'invoice','timeline-event','enrolment','teacher-availability'],
                'allow' => true,
                'roles' => ['staff'],
            ],
            [
                'controllers'=>['program', 'city', 'location','province','country'],
                'allow' => true,
                'roles' => ['staff'],
                'actions'=>['index', 'view']
            ],
			[
                'controllers'=>['user'],
                'allow' => false,
            ],
            [
                'allow' => true,
                'roles' => ['administrator'],
            ]
        ]
    ]
];

if (YII_ENV_DEV) {
    $config['modules']['gii'] = [
        'class'=>'yii\gii\Module',
        'generators' => [
            'crud' => [
                'class'=>'yii\gii\generators\crud\Generator',
                'templates'=>[
                    'yii2-starter-kit' => Yii::getAlias('@backend/views/_gii/templates')
                ],
                'template' => 'yii2-starter-kit',
                'messageCategory' => 'backend'
            ]
        ]
    ];
}

return $config;
