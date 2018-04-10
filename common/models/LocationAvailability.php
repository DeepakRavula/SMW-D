<?php

namespace common\models;



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

    public static function find()
    {
        return new \common\models\query\LocationAvailabilityQuery(get_called_class());
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
           
               $fromTime=(new \DateTime($this->fromTime))->format('H:i:s');
                $toTime=(new \DateTime($this->toTime))->format('H:i:s');
                
                if($toTime<=$fromTime)
           {
               return $this->addError($attributes, "To Time should be greater than From Time");
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
