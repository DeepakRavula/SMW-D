<?php

use yii\db\Migration;
use common\models\LocationAvailability;
class m180209_100941_update_type_location_availability extends Migration
{
    public function up()
    {
         $locationAvailabilitys = LocationAvailability::find()
            ->all();
        foreach ($locationAvailabilitys as $locationAvailability) {
            $locationAvailability->updateAttributes([
                'type' => LocationAvailability::TYPE_OPERATION_TIME
            ]);
             $newLocationAvailability = new LocationAvailability();
                $newLocationAvailability->locationId = $locationAvailability->locationId;            $newLocationAvailability->day = $locationAvailability->day;
                $newLocationAvailability->type = LocationAvailability::TYPE_SCHEDULE_TIME;           $newLocationAvailability->fromTime = $locationAvailability->fromTime;                $newLocationAvailability->toTime = $locationAvailability->toTime;
                $newLocationAvailability->save();
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
