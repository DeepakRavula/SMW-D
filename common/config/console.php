<?php

return [
	'components' => [
		'backup' => [
			'class' => 'demi\backup\Component',
			'backupsFolder' => dirname(dirname(__DIR__)) . '/backups',
    	],
		'errorHandler' => [
			'class' => 'baibaratsky\yii\rollbar\console\ErrorHandler',
		],
		'urlManager' => [
			 'class' => 'yii\web\UrlManager',
			 'baseUrl' => env('CONSOLE_BASE_URL'),
			 'enablePrettyUrl' => true,
			 'showScriptName' => false,
		  ],
		'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'common\models\User',
        ],
	],
];
