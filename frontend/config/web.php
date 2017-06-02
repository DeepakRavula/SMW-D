<?php

$config = [
    'homeUrl' => Yii::getAlias('@frontendUrl'),
    'controllerNamespace' => 'frontend\controllers',
    'defaultRoute' => 'site/index',
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
    ],
    'components' => [
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
            'cookieValidationKey' => env('FRONTEND_COOKIE_VALIDATION_KEY'),
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
                'controllers' => ['/user/sign-in'],
                'allow' => true,
                'roles' => ['?'],
                'actions' => ['login', 'request-password-reset', 'reset-password'],
            ],
			[
                'controllers' => ['/user/sign-in'],
                'allow' => true,
                'roles' => ['teacher'],
                'actions' => ['logout', 'profile', 'account'],
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
                'controllers' => ['schedule'],
                'allow' => true,
                'roles' => ['teacher'],
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
