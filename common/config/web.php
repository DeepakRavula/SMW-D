<?php
$config = [
    'components' => [
        'assetManager' => [
            'class' => 'yii\web\AssetManager',
            'linkAssets' => false,
            'appendTimestamp' => YII_ENV_DEV
        ]
    ],
    'as locale' => [
        'class' => 'common\behaviors\LocaleBehavior',
        'enablePreferredLanguage' => true
    ],
    'as access' => [
        'class' => 'mdm\admin\components\AccessControl',
        'allowActions' => [
            'student/*',
            'site/*',
            'admin/*',
			'user/*',
			'program/*',
	    	'teacher/*',
			'owner/*',
			'administrator/*',
	    	'qualification/*',
			'lesson/*',
			'invoice/*',
            'release-notes/*',
            'reminder-note/*',
			'location/*',
			'city/*',
			'province/*', 
            'tax-code/*',
			'country/*',
            'system-information/*',
            'gii/*',
            'sign-in/*',
            'timeline-event/*',
	    	'debug/*',
	    	'schedule/*',
	    	'enrolment/*',
	    	'teacher-availability/*',
	    	'cron/*',
			'group-course/*',
			'group-lesson/*',
			'group-enrolment/*',
			'holiday/*',
			'professional-development-day/*',
			'calendar/*',
			'blog/*',
			'payment/*',
            'dashboard/*',
			'course/*',
			'log/*',

            // The actions listed here will be allowed to everyone including guests.
            // So, 'admin/*' should not appear here in the production, of course.
            // But in the earlier stages of your development, you may probably want to
            // add a lot of actions here until you finally completed setting up rbac,
            // otherwise you may not even take a first step.
        ]
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
