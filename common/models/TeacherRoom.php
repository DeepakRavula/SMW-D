<?php

namespace common\models;

use common\models\query\TeacherRoomQuery;

use Yii;

/**
 * This is the model class for table "teacher_room".
 *
 * @property string $id
 * @property integer $teacherAvailabilityId
 * @property integer $classroomId
 */
class TeacherRoom extends \yii\db\ActiveRecord
{
    public $availabilityId;
    public $teacher_location_id;
    public $day;
    public $from_time;
    public $to_time;
    const SCENARIO_AVAILABIITY_EDIT = 'availability-edit';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'teacher_room';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['day', 'from_time', 'to_time'], 'required'],
            [['classroomId', 'teacherAvailabilityId'], 'integer'],
            [['from_time', 'to_time'], 'validateAvailabilityOverlap', 'on' => self::SCENARIO_AVAILABIITY_EDIT ],
            [['classroomId'], 'validateClassroomAvailability', 'on' => self::SCENARIO_AVAILABIITY_EDIT ],
            [['from_time'], function ($attribute, $params) {
                $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
                $locationAvailability = LocationAvailability::findOne(['locationId' => $locationId, 'day' => $this->day]);
                $fromTime = (new \DateTime($this->from_time))->format('H:i:s');
                if (empty($locationAvailability)) {
                    return $this->addError($attribute, 'Please choose from time within the operating hours ');
                } elseif ($fromTime < $locationAvailability->fromTime || $locationAvailability->toTime < $fromTime) {
                    return $this->addError($attribute, 'Please choose from time within the operating hours ' . Yii::$app->formatter->asTime($locationAvailability->fromTime) . ' to ' . Yii::$app->formatter->asTime($locationAvailability->toTime));
                }
            },
            ],
            [['to_time'], function ($attribute, $params) {
                $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
                $locationAvailability = LocationAvailability::findOne(['locationId' => $locationId, 'day' => $this->day]);
                $toTime = (new \DateTime($this->to_time))->format('H:i:s');
                if (empty($locationAvailability)) {
                    return $this->addError($attribute, 'Please choose from time within the operating hours ');
                } elseif ($toTime > $locationAvailability->toTime || $toTime < $locationAvailability->fromTime) {
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
                if ($toTime < $fromTime) {
                    return $this->addError($attribute, 'To time must be greater than "From Time"');
                }
            },
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'day' => 'Day',
            'classroomId' => 'Classroom',
        ];
    }

    public static function find()
    {
        return new TeacherRoomQuery(get_called_class());
    }

    public function getClassroom()
    {
        return $this->hasOne(Classroom::className(), ['id' => 'classroomId'])
            ->onCondition(['classroom.isDeleted' => false]);
    }

    public function getTeacherAvailability()
    {
        return $this->hasOne(TeacherAvailability::className(), ['id' => 'teacherAvailabilityId']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'teacherId']);
    }

    public function getUserLocation()
    {
        return $this->hasOne(UserLocation::className(), ['user_id' => 'id'])
            ->via('user');
    }

    public function validateClassroomAvailability($attribute)
    {
        if (! empty($this->classroomId)) {
            $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
            $teacherRooms        = TeacherRoom::find()
                ->location($locationId)
                ->andWhere(['NOT', ['teacher_room.id' => $this->id]])
                ->day($this->day)
                ->between($this->from_time, $this->to_time)
                ->andWhere(['classroomId' => $this->classroomId])
                ->all();
            if (!empty($teacherRooms)) {
                return $this->addError($attribute, 'Classroom already chosen');
            } 
        }
    }

    public function validateAvailabilityOverlap($attribute)
    {
        $availabilities = TeacherAvailability::find()
            ->andWhere(['day' => $this->day, 'teacher_location_id' => $this->teacher_location_id])
            ->andWhere(['NOT', ['id' => $this->availabilityId]])
            ->between($this->from_time, $this->to_time)
            ->notDeleted()
            ->all();
        
        if (!empty($availabilities)) {
            return $this->addError($attribute, 'Availability overlapped');
        }
    }
}
