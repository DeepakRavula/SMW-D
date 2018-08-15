<?php

namespace console\controllers;

use yii\console\Controller;
use Yii;
use yii\db\Migration;
use common\models\User;

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
        $file = Yii::getAlias('@console') . '/sql/wipe_location_transactional_data.sql';
        
        $migration = new Migration();
        return $migration->execute(file_get_contents($file), [':locationToWipe' => $this->locationId]);
    }

    public function actionWipeCustomers()
    {
        $customers = User::find()
            ->excludeWalkin()
            ->location(8)
            ->all();
        foreach ($customers as $customer) {
            $customer->delete();
        }
        return true;
    }
}