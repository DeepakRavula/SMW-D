<?php
use yii\helpers\ArrayHelper;
use common\models\User;
use common\models\UserLocation;
use yii\web\ForbiddenHttpException;
$config = [
    'homeUrl' => Yii::getAlias('@backendUrl'),
    'controllerNamespace' => 'backend\controllers',
    'defaultRoute' => 'dashboard/index',
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
            'cookieValidationKey' => env('BACKEND_COOKIE_VALIDATION_KEY'),
			'baseUrl' => '/admin',
        ],
        'urlManager' => [
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
    'as beforeAction' => 'common\behaviors\GlobalBeforeAction',
    'as globalAccess' => [
        'class' => '\common\behaviors\GlobalAccessBehavior',
        'rules' => [
            [
                'controllers' => ['sign-in'],
                'allow' => true,
                'roles' => ['?'],
                'actions' => ['login', 'request-password-reset', 'reset-password'],
            ],
            [
                'controllers' => ['sign-in'],
                'allow' => true,
                'roles' => ['@'],
                'actions' => ['logout', 'profile', 'account'],
            ],
            [
                'controllers' => ['site'],
                'allow' => true,
                'roles' => ['?', '@'],
                'actions' => ['error'],
            ],
            [
                'controllers' => ['debug/default'],
                'allow' => true,
                'roles' => ['?'],
            ],
            [
                'controllers' => ['user','student-birthday'],
                'allow' => true,
                'roles' => ['administrator', 'staffmember'],
            ],
			[
                'controllers' => ['student-birthday'],
                'allow' => true,
                'roles' => ['viewReport'],
            ],
            [
                'controllers' => [
					'schedule', 'student', 'exam-result','note',
					'classroom-unavailability', 'calendar','item', 
					'item-category', 'daily-schedule','user',
					'release-notes', 'lesson', 'invoice', 'timeline-event',
					'enrolment','teacher-room', 'program', 'customer', 'email',
					'teacher-availability', 'group-course', 'group-lesson',
					'group-enrolment', 'payment', 'course', 'log',
					'invoice-line-item', 'holiday', 'qualification',
					'customer-payment-preference','tax-code', 'vacation', 
					'customer-discount', 'classroom', 'report', 'teacher-rate',
					'private-lesson','student-birthday', 'teacher-unavailability',
					'print', 'user-contact','teacher-substitute',
				],
                'allow' => true,
                'roles' => ['listCustomer', 'listEnrolment', 'listGroupLesson', 'listInvoice', 'listItem', 'listOwner'],
            ],
			[
                'controllers' => ['dashboard'],
                'allow' => true,
                'roles' => ['viewDashboard'],
                'actions' => ['index'],
            ],
			[
                'controllers' => ['permission', 'release-notes', 'reminder-note'],
                'allow' => true,
                'roles' => ['administrator'],
            ],
            [
                'controllers' => ['program', 'city', 'location', 'province', 'country', 'discount'],
                'allow' => true,
                'roles' => ['administrator'],
            ],
            [
                'controllers' => ['blog'],
                'allow' => true,
                'roles' => ['staffmember'],
                'actions' => ['list'],
            ],
            [
                'controllers' => ['user'],
                'allow' => false,
            ],
            [
                'allow' => true,
                'roles' => ['administrator'],
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
