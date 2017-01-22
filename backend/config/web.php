<?php

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
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'request' => [
            'cookieValidationKey' => env('BACKEND_COOKIE_VALIDATION_KEY'),
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
    'on beforeAction' => function ($event) {
        $location_id = Yii::$app->session->get('location_id');
        if (empty($location_id)) {
            $roles = yii\helpers\ArrayHelper::getColumn(
                Yii::$app->authManager->getRolesByUser(Yii::$app->user->id),
                'name'
            );
            $role = end($roles);
            if ($role && $role !== common\models\User::ROLE_ADMINISTRATOR) {
                $userLocation = common\models\UserLocation::findOne(['user_id' => Yii::$app->user->id]);
                Yii::$app->session->set('location_id', $userLocation->location_id);
            } else {
                Yii::$app->session->set('location_id', '1');
            }
        }
        $unReadNotes = [];
        $latestNotes = common\models\ReleaseNotes::latestNotes();
        if (!empty($latestNotes)) {
            $unReadNotes = common\models\ReleaseNotesRead::findOne(['release_note_id' => $latestNotes->id, 'user_id' => Yii::$app->user->id]);
        }
        Yii::$app->view->params['latestNotes'] = $latestNotes;
        Yii::$app->view->params['unReadNotes'] = $unReadNotes;
    },
    'as globalAccess' => [
        'class' => '\common\behaviors\GlobalAccessBehavior',
        'rules' => [
            [
                'controllers' => ['calendar'],
                'allow' => true,
                'roles' => ['?'],
                'actions' => ['view'],
            ],
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
                'controllers' => ['user'],
                'allow' => true,
                'roles' => ['administrator', 'staffmember'],
            ],
            [
                'controllers' => ['schedule', 'student', 'exam-result','note', 'payment-frequency',
					'release-notes', 'lesson', 'invoice', 'timeline-event', 'enrolment', 
					'teacher-availability', 'group-course', 'group-lesson', 'group-enrolment', 
					'payment', 'course', 'dashboard', 'log', 'invoice-line-item', 'holiday', 
					'professional-development-day', 'tax-code', 'vacation'
				],
                'allow' => true,
                'roles' => ['staffmember'],
            ],
            [
                'controllers' => ['program', 'city', 'location', 'province', 'country'],
                'allow' => true,
                'roles' => ['staffmember'],
                'actions' => ['index', 'view'],
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
            [
                'controllers' => ['release-notes', 'reminder-note'],
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
