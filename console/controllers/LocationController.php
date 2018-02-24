<?php

namespace console\controllers;

use yii\console\Controller;
use Yii;

class LocationController extends Controller
{
    public $locationId;
    
    public function options($actionID)
    {
        return array_merge(parent::options($actionID),
            $actionID == 'wipe-transactional-data' ? ['locationId'] : []
        );
    }
    
    public function actionWipeTransactionalData()
    {
        $str = implode("\n", file(Yii::getAlias('@console') . '/config/wipe_location_transactional_data.sql'));

        $fp = fopen(Yii::getAlias('@console') . '/config/wipe_location_transactional_data.sql', 'w');

        // Replace something in the file string - this is a VERY simple example
        $str = str_replace("locationToWipe", $this->locationId, $str);
        fwrite($fp, $str, strlen($str));
        fclose($fp);
        $migration = new \yii\db\Migration();
        return $migration->execute($fp);
    }
}