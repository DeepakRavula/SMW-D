<?php

return [
    'id' => 'console',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'console\controllers',
    'controllerMap' => [
        'command-bus' => [
            'class' => 'trntv\bus\console\BackgroundBusController',
        ],
        'message' => [
            'class' => 'console\controllers\ExtendedMessageController',
        ],
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationPath' => '@common/migrations/db',
            'migrationTable' => '{{%system_db_migration}}',
        ],
        'sample-data-migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationPath' => '@common/migrations/sample-data',
            'migrationTable' => '{{%system_sample_data_migration}}',
        ],
        'rbac-migrate' => [
            'class' => 'console\controllers\RbacMigrateController',
            'migrationPath' => '@common/migrations/rbac/',
            'migrationTable' => '{{%system_rbac_migration}}',
            'templateFile' => '@common/rbac/views/migration.php',
        ],
        'one-off' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationPath' => '@common/migrations/one-off/',
            'migrationTable' => '{{%system_one_off_migration}}',
        ],
        'queue-migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationPath' => null,
            'migrationNamespaces' => [
                // ...
                'yii\queue\db\migrations',
            ],
        ],
    ],
];
