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

    public function beforeSave($insert)
    {
        self::deleteAll(['teacherAvailabilityId' => $this->teacherAvailabilityId]);
		return parent::beforeSave($insert);
    }

	public function getClassroom()
    {
        return $this->hasOne(Classroom::className(), ['id' => 'classroomId']);
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
        if (! empty ($this->classroomId)) {
            $locationId = Yii::$app->session->get('location_id');
            $teacherRooms        = TeacherRoom::find()
                ->location($locationId)
                ->day($this->day)
                ->where(['classroomId' => $this->classroomId])
                ->all();
            foreach ($teacherRooms as $teacherRoom) {
                $fromTime = (new \DateTime($teacherRoom->teacherAvailability->from_time))->format('h:i A');
                $toTime   = (new \DateTime($teacherRoom->teacherAvailability->to_time))->format('h:i A');
                $start    = new \DateTime($this->from_time);
                $end      = new \DateTime($this->to_time);
                $interval = new \DateInterval('PT15M');
                $hours    = new \DatePeriod($start, $interval, $end);
                $this->checkClassroomAvailability($hours, $fromTime, $toTime, $attribute);
            }
        }
    }

    public function checkClassroomAvailability($hours, $fromTime, $toTime, $attribute)
    {
        $availableHours = [];
        foreach ($hours as $hour) {
            $availableHours[] = Yii::$app->formatter->asTime($hour);
        }

        if (in_array($fromTime, $availableHours) || in_array($toTime, $availableHours)) {
            return $this->addError($attribute,'Classroom Already Choosen!');
        }
    }

    public function validateAvailabilityOverlap($attribute)
    {
        $availabilities = TeacherAvailability::find()
            ->where(['day' => $this->day, 'teacher_location_id' => $this->teacher_location_id])
            ->andWhere(['OR', 
                [
                    'between', 'from_time', (new \DateTime($this->from_time))->format('H:i:s'),
                    (new \DateTime($this->to_time))->format('H:i:s')
                ],
                [
                    'between', 'to_time', (new \DateTime($this->from_time))->format('H:i:s'),
                    (new \DateTime($this->to_time))->format('H:i:s')
                ],
                [
                    'AND',
                    [
                        '<=', 'from_time', $this->from_time
                    ],
                    [
                        '>=', 'to_time', $this->to_time
                    ]
                    
                ]
            ])
            ->all();
        
        if (!empty($availabilities)) {
            return $this->addError($attribute,'Availability overlaped');
        }
    }
}
