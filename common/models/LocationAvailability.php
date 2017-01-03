<?php

namespace common\models;

use Yii;

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
            [['locationId', 'day'], 'required'],
            [['locationId', 'day'], 'integer'],
            [['fromTime', 'toTime'], 'safe'],
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
            'fromTime' => 'From Time',
            'toTime' => 'To Time',
        ];
    }
}
