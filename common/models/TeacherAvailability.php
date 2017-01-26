<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "teacher_availability_day".
 *
 * @property string $id
 * @property int $teacher_location_id
 * @property int $day
 * @property string $from_time
 * @property string $to_time
 */
class TeacherAvailability extends \yii\db\ActiveRecord
{
    public $name;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teacher_availability_day';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['day', 'from_time', 'to_time'], 'required'],
            [['day'], 'integer'],
			[['teacher_location_id'], 'safe'],
            [['from_time'], function ($attribute, $params) {
                $locationId = Yii::$app->session->get('location_id');
                $locationAvailability = LocationAvailability::findOne(['locationId' => $locationId, 'day' => $this->day]);
                $fromTime = (new \DateTime($this->from_time))->format('H:i:s');
                if(empty($locationAvailability)) {
                    return $this->addError($attribute, 'Please choose from time within the operating hours ');
                }else if ($fromTime < $locationAvailability->fromTime || $locationAvailability->toTime < $fromTime) {
                    return $this->addError($attribute, 'Please choose from time within the operating hours ' . Yii::$app->formatter->asTime($locationAvailability->fromTime) . ' to ' . Yii::$app->formatter->asTime($locationAvailability->toTime));
                }
            },
            ],
            [['to_time'], function ($attribute, $params) {
                $locationId = Yii::$app->session->get('location_id');
                $locationAvailability = LocationAvailability::findOne(['locationId' => $locationId, 'day' => $this->day]);
                $toTime = (new \DateTime($this->to_time))->format('H:i:s');
                if(empty($locationAvailability)) {
                    return $this->addError($attribute, 'Please choose from time within the operating hours ');
                }else if ($toTime > $locationAvailability->toTime || $toTime < $locationAvailability->fromTime) {
                    return $this->addError($attribute, 'Please choose from time within the operating hours ' . Yii::$app->formatter->asTime($locationAvailability->fromTime) . ' to ' . Yii::$app->formatter->asTime($locationAvailability->toTime));
                }
            },
            ],
            [['from_time'], function ($attribute, $params) {
                $fromTime = (new \DateTime($this->from_time))->format('H:i:s');
                $toTime = (new \DateTime($this->to_time))->format('H:i:s');
                if ($fromTime > $toTime) {
                    return $this->addError($attribute, 'From time must be less than "To Time"');
                }
            },
            ],
			[['to_time'], function ($attribute, $params) {
                $fromTime = (new \DateTime($this->from_time))->format('H:i:s');
                $toTime = (new \DateTime($this->to_time))->format('H:i:s');
				if($toTime < $fromTime) {
                    return $this->addError($attribute, 'To time must be greater than "From Time"');
				}
            },
            ],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'teacher_location_id' => 'Teacher Location',
            'day' => 'Day',
            'from_time' => 'From Time',
            'to_time' => 'To Time',
        ];
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

    public function IsAvailableAtLocation($teacher_location_id)
    {
        return $this->find()
                ->where(['teacher_location_id' => $teacher_location_id])
                ->exists();
    }

    public function getTeacher()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id'])
            ->viaTable('user_location', ['id' => 'teacher_location_id']);
    }

    public function getUserLocation()
    {
        return $this->hasOne(UserLocation::className(), ['id' => 'teacher_location_id']);
    }
}
