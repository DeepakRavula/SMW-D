<?php

use yii\db\Migration;
use common\models\Location;
/**
 * Class m181219_094007_enable_cron_prod_location
 */
class m181219_094007_enable_cron_prod_location extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $locationIds = [4, 9, 17, 18, 19, 20, 21];
        $locations = Location::find()
                ->notDeleted()
                ->andWhere(['id'=> $locationIds])
                ->andWhere(['location.isEnabledCron' =>  false])
                ->all();
        foreach ($locations as $location) {
            $location->updateAttributes(['isEnabledCron' => true]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181219_094007_enable_cron_prod_location cannot be reverted.\n";

        return false;
    }
}
