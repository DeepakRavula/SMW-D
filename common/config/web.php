<?php

$config = [
    'components' => [
         'errorHandler' => [
            'class' => 'baibaratsky\yii\rollbar\web\ErrorHandler',
        ],
        'assetManager' => [
            'class'=>'yii\web\AssetManager',
            'linkAssets' => false,
            'appendTimestamp' => YII_ENV_DEV,
            'bundles'=>[
                'insolita\wgadminlte\ExtAdminlteAsset'=>[
                    'depends'=>[
                        'yii\web\YiiAsset',
                        'common\assets\AdminLte',
                        'insolita\wgadminlte\JsCookieAsset'
                    ]
                ],
                'insolita\wgadminlte\JsCookieAsset'=>[
                    'depends'=>[
                        'yii\web\YiiAsset',
                        'common\assets\AdminLte',
                    ]
                ],
            ],
        ],
        'request' => [
            'enableCsrfValidation' => false,
        ],
    ],
    'as locale' => [
        'class' => 'common\behaviors\LocaleBehavior',
        'enablePreferredLanguage' => true,
    ],
    'as access' => [
        'class' => 'mdm\admin\components\AccessControl',
        'allowActions' => [
            'student/*',
            'student-birthday/*'            ,
            'site/*',
            'admin/*',
            'user/*',
            'program/*',
            'teacher/*',
            'owner/*',
            'administrator/*',
            'qualification/*',
            'lesson/*',
            'exploded-lesson/*',
            'extra-lesson/*',
            'item/*',
            'item-category/*',
            'invoice/*',
            'invoice-line-item/*',
            'release-notes/*',
            'reminder-note/*',
            'location/*',
            'city/*',
            'province/*',
            'tax-code/*',
            'tax-status/*',
            'country/*',
            'system-information/*',
            'gii/*',
            'sign-in/*',
            'timeline-event/*',
            'debug/*',
            'schedule/*',
            'schedule2/*',
            'enrolment/*',
            'teacher-availability/*',
            'teacher-unavailability/*',
            'cron/*',
            'group-course/*',
            'group-lesson/*',
            'group-enrolment/*',
            'holiday/*',
            'customer-discount/*',
            'calendar/*',
            'blog/*',
            'payment/*',
            'dashboard/*',
            'course/*',
            'log/*',
            'vacation/*',
            'calendar-event-color/*',
            'classroom/*',
            'exam-result/*',
            'note/*',
            'teacher-room/*',
            'teacher-substitute/*',
            'discount/*',
            'classroom-unavailability/*',
            'report/*',
            'teacher-rate/*',
            'customer-payment-preference/*',
            'private-lesson/*',
            'daily-schedule/*',
            'text-template/*',
            'email/*',
            'print/*',
            'customer/*',
            'user-contact/*',
            'daily-schedule/*',
            'permission/*',
            'user-pin/*',
            'email-template/*',
	    'test-email/*',
            'reverse-enrolment/*',
	    'proforma-invoice/*',
        'unscheduled-lesson/*',
        'terms-of-service/*',
        'referral-source/*',
        'gridview/export/*',
            // The actions listed here will be allowed to everyone including guests.
            // So, 'admin/*' should not appear here in the production, of course.
            // But in the earlier stages of your development, you may probably want to
            // add a lot of actions here until you finally completed setting up rbac,
            // otherwise you may not even take a first step.
        ],
    ],
];

if (YII_DEBUG) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.33.1', '172.17.42.1', '172.17.0.1', '10.0.2.2'],
    ];
}

if (YII_ENV_DEV) {
    $config['modules']['gii'] = [
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.33.1', '172.17.42.1', '172.17.0.1', '10.0.2.2'],
    ];
}

return $config;
