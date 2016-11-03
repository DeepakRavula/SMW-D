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
            [['teacher_location_id', 'day', 'from_time', 'to_time'], 'required'],
            [['teacher_location_id', 'day'], 'integer'],
            [['from_time'], function ($attribute, $params) {
                $locationId = Yii::$app->session->get('location_id');
                $location = Location::findOne(['id' => $locationId]);
                $fromTime = (new \DateTime($this->from_time))->format('H:i:s');
                if ($fromTime < $location->from_time) {
                    return $this->addError($attribute, 'Operating hours Start Time '.Yii::$app->formatter->asTime($location->from_time));
                }
            },
            ],
            [['to_time'], function ($attribute, $params) {
                $locationId = Yii::$app->session->get('location_id');
                $location = Location::findOne(['id' => $locationId]);
                $toTime = (new \DateTime($this->to_time))->format('H:i:s');
                if ($toTime > $location->to_time) {
                    return $this->addError($attribute, 'Operating hours To Time '.Yii::$app->formatter->asTime($location->to_time));
                }
            },
            ],
            [['from_time'], function ($attribute, $params) {
                $fromTime = (new \DateTime($this->from_time))->format('H:i:s');
                $toTime = (new \DateTime($this->to_time))->format('H:i:s');
                if ($fromTime > $toTime) {
                    return $this->addError($attribute, 'From Time must be less than "To Time"');
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
        1 => 'Monday',
                'Tuesday',
                'Wednesday',
                'Thursday',
                'Friday',
                'Saturday',
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
