<?php

namespace console\controllers;

use yii\console\Controller;
use Yii;
use yii\db\Migration;

class StudentCsvController extends Controller
{
    public function actionWipeTransactionalData()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        if (env('YII_ENV') === 'dev') {
            $files = [];
            $files[] = Yii::getAlias('@console') . '/sql/wipe_csv_transactional_data_course.sql';
            $files[] = Yii::getAlias('@console') . '/sql/wipe_csv_transactional_data_invoice.sql';
            $files[] = Yii::getAlias('@console') . '/sql/wipe_csv_transactional_data_user.sql';

            foreach ($files as $file) {
                $migration = new Migration();
                $migration->execute(file_get_contents($file));
            }
            echo 'Student csv transactional data has been cleared succesfully!';
        } else {
            echo 'This can be done in non production environment only!';
        }
    }
}