<?php

namespace common\models;

use Yii;
use common\components\validators\availability\LocationAvailabilityValidator;


/**
 * This is the model class for table "location_availability".
 *
 * @property string $id
 * @property string $locationId
 * @property integer $day
 * @property string $fromTime
 * @property string $toTime
 */
class LocationAvailability extends \yii\db\ActiveRecord
{
    const DEFAULT_FROM_TIME = '09:00:00';
    const DEFAULT_TO_TIME   = '17:00:00';
    const TYPE_OPERATION_TIME =1;
    const TYPE_SCHEDULE_TIME =2;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'location_availability';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['locationId', 'day','type'], 'required'],
            [['locationId', 'day','type'], 'integer'],
            [['fromTime', 'toTime'], 'safe'],
            [['fromTime'], 'validateToTime'] ,
            [['toTime'], 'validateToTime'] 
            
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'locationId' => 'Location ID',
            'day' => 'Day',
            'type' => 'Type',
            'fromTime' => 'From Time',
            'toTime' => 'To Time',
        ];
    }
        public function validateToTime($attributes)
    {
           if($this->toTime<=$this->fromTime)
           {
               return $this->addError($attributes, "End Time cannot be greater than StartTime");
           }
                
    }
    public static function getWeekdaysList()
    {
        return [
        1   =>  'Monday',
                'Tuesday',
                'Wednesday',
                'Thursday',
                'Friday',
                'Saturday',
                'Sunday'
        ];
    }
}
