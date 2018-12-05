<?php

namespace console\controllers;

use yii\console\Controller;
use Yii;
use yii\db\Migration;
use common\models\User;
use common\models\discount\LessonDiscount;
use common\models\Location;
use common\models\LocationAvailability;
use yii\helpers\Console;

class LocationMigrationController extends Controller
{
    public $locationId;
    
    public function init() 
    {
        parent::init();
		$user = User::findByRole(User::ROLE_BOT);
		$botUser = end($user);
        Yii::$app->user->setIdentity(User::findOne(['id' => $botUser->id]));
    }
    public function actionMigrateLocation()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $locationIds = [2,3,5,6];
       $locations = Location::find()
                    ->andWhere(['id' => $locationIds])
                    ->all();
                    $count = count($locations);
                    Console::startProgress(0, $count, 'Updating Locations...');            
         $locationEmails = [];            
        foreach($locations as $location) {
            $locationEmails[$location->id] = $location->email;
            $location->email = "new".$location->id."sample@example.com";
            $location->royaltyValue = $location->royalty->value;
            $location->advertisementValue = $location->advertisement->value;
            if(!$location->save()) {
                print_r($location->getErrors());
            }
        }
     foreach($locations as $location) {
            Console::output("processing: " . $location->name, Console::FG_GREEN, Console::BOLD);
            $newLocation = new Location;
            $newLocation->name = $location->name;
            $newLocation->address = $location->address;
            $newLocation->phone_number = $location->phone_number;
            $newLocation->email = $locationEmails[$location->id];
            $newLocation->city_id = $location->city_id;
            $newLocation->province_id = $location->province_id;
            $newLocation->postal_code = $location->postal_code;
            $newLocation->country_id = $location->country_id;
            $newLocation->slug = $location->slug;
            $newLocation->conversionDate = $location->conversionDate;
            $newLocation->isDeleted = $location->isDeleted;
            $newLocation->isEnabledCron = $location->isEnabledCron;
            $location->name = $location->name."old";
            $location->slug = $location->slug."old";
            $newLocation->royaltyValue = $location->royalty->value;
            $newLocation->advertisementValue = $location->advertisement->value;
            $location->save();
            if(!$newLocation->save())
            {
                print_r($newLocation->getErrors());
            }
            $locationAvailabilities = $location->locationAvailabilities;
            foreach ($locationAvailabilities as $locationAvailability){
                $newLocationAvailability = new LocationAvailability();
                $newLocationAvailability->fromTime   = $locationAvailability->fromTime;
                $newLocationAvailability->toTime     = $locationAvailability->toTime;
                $newLocationAvailability->locationId = $newLocation->id;
                $newLocationAvailability->day = $locationAvailability->day;
                $newLocationAvailability->type = $locationAvailability->type;
                if(!$newLocationAvailability->save()){
                    print_r($newLocationAvailability->getErrors());
                }
            }

                
        $owners = User::find()
        ->joinWith('userLocation ul')
        ->join('INNER JOIN', 'rbac_auth_assignment raa', 'raa.user_id = user.id')
        ->andWhere(['raa.item_name' => 'owner'])
        ->andWhere(['ul.location_id' => $location->id])
        ->notDeleted()
        ->active()
        ->all();
        foreach($owners as $owner) {
            $ownerUserLocation = $owner->userLocation;
            $ownerUserLocation->location_id = $newLocation->id;
            if(!$ownerUserLocation->save()) {
                print_r($ownerUserLocation->getErrors());
            }
        }
    }
      
    }
   
      
}