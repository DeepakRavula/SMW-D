<?php

namespace console\controllers;

use yii\console\Controller;
use Yii;
use yii\db\Migration;

class StudentCsvController extends Controller
{
    public function actionWipeTransactionalData()
    {
        $file1 = Yii::getAlias('@console') . '/sql/wipe_csv_transactional_data_course.sql';
        $file2 = Yii::getAlias('@console') . '/sql/wipe_csv_transactional_data_invoice.sql';
        $file3 = Yii::getAlias('@console') . '/sql/wipe_csv_transactional_data_user.sql';
        
        $migration = new Migration();
        $migration->execute(file_get_contents($file1));
        $migration->execute(file_get_contents($file2));
        return $migration->execute(file_get_contents($file3));
    }
}