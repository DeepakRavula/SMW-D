<?php

return [
    'components' => [
        'backup' => [
            'class' => 'demi\backup\Component',
            'backupsFolder' => env('BACK_UP_FOLDER'),
            'backupFilename' => 'Y_m_d-H_i_s',
            'expireTime' => 700000,
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
            'enableSession' => false,
        ],
    ],
];
