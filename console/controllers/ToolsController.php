<?php
namespace console\controllers;

class ToolsController extends \yii\console\Controller
{
    public function actionBackup()
    {
        /** @var \demi\backup\Component $backup */
        $backup = \Yii::$app->backup;
        
        $file = $backup->create();

        $this->stdout('Backup file created: ' . $file . PHP_EOL, \yii\helpers\Console::FG_GREEN);
    }
} 