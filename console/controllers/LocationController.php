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
            $actionID == 'wipe-transactional-data' || 'wipe-customers' ? ['locationId'] : []
        );
    }
    
    public function actionWipeTransactionalData()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        
        $file = Yii::getAlias('@console') . '/sql/wipe_location_transactional_data.sql';
        
        $migration = new Migration();
        return $migration->execute(file_get_contents($file), [':locationToWipe' => $this->locationId]);
    }
    
    public function actionWipeUnlinkedTransactionalData()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        
        $file = Yii::getAlias('@console') . '/sql/wipe_unlinked_transactional_data.sql';
        
        $migration = new Migration();
        return $migration->execute(file_get_contents($file));
    }

    public function actionWipeCustomers()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $customers = User::find()
            ->excludeWalkin()
            ->location($this->locationId)
            ->all();
        foreach ($customers as $customer) {
            $customer->delete();
        }
        return true;
    }
}