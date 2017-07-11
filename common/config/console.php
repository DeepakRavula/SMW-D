<?php

return [
	'components' => [
		'errorHandler' => [
			'class' => 'baibaratsky\yii\rollbar\console\ErrorHandler',
		],
		'urlManager' => [
			 'class' => 'yii\web\UrlManager',
			 'baseUrl' => env('CONSOLE_BASE_URL'),
			 'enablePrettyUrl' => true,
			 'showScriptName' => false,
		  ],
	],
];
