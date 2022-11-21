<?php

use yii\db\Migration;
use common\models\LocationAvailability;
class m180209_100941_update_type_location_availability extends Migration
{
    public function up()
    {
         $locationAvailabilities = LocationAvailability::find()
            ->all();
        foreach ($locationAvailabilities as $locationAvailability) {
            $locationAvailability->updateAttributes([
                'type' => LocationAvailability::TYPE_OPERATION_TIME
            ]);
                $scheduleAvailability = new LocationAvailability();
                $scheduleAvailability->locationId = $locationAvailability->locationId;               $scheduleAvailability->day = $locationAvailability->day;
                $scheduleAvailability->type = LocationAvailability::TYPE_SCHEDULE_TIME;              $scheduleAvailability->fromTime = $locationAvailability->fromTime;                   $scheduleAvailability->toTime = $locationAvailability->toTime;
                $scheduleAvailability->save();
        }     
    }

    public function down()
    {
        echo "m180209_100941_update_type_location_availability cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
