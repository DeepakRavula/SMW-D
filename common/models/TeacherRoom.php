<?php

namespace common\models;

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
            [['classroomId', 'teacherAvailabilityId'], 'integer'],
			[['classroomId'], function ($attribute, $params) {
                $teacherAvailability = TeacherAvailability::findOne($this->teacherAvailabilityId);
                $teacherRooms        = TeacherRoom::findAll(['classroomId' => $this->classroomId]);
                foreach ($teacherRooms as $teacherRoom) {
                    if ($teacherRoom->teacherAvailability->day === $teacherAvailability->day) {
                        $fromTime = (new \DateTime($teacherRoom->teacherAvailability->from_time))->format('h:i A');
                        $toTime   = (new \DateTime($teacherRoom->teacherAvailability->to_time))->format('h:i A');
                        $start    = new \DateTime($teacherAvailability->from_time);
                        $end      = new \DateTime($teacherAvailability->to_time);
                        $interval = new \DateInterval('PT15M');
                        $hours    = new \DatePeriod($start, $interval, $end);
                        $this->checkClassroomAvailability($hours, $fromTime, $toTime);
                        
                        $fromTime = (new \DateTime($teacherAvailability->from_time))->format('h:i A');
                        $toTime   = (new \DateTime($teacherAvailability->to_time))->format('h:i A');
                        $start    = new \DateTime($teacherRoom->teacherAvailability->from_time);
                        $end      = new \DateTime($teacherRoom->teacherAvailability->to_time);
                        $hours    = new \DatePeriod($start, $interval, $end);
                        $this->checkClassroomAvailability($hours, $fromTime, $toTime);
                    }
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
            'classroomId' => 'Classroom ID',
        ];
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

    public function checkClassroomAvailability($hours, $fromTime, $toTime)
    {
        $availableHours = [];
        foreach ($hours as $hour) {
            $availableHours[] = Yii::$app->formatter->asTime($hour);
        }
        
        if (in_array($fromTime, $availableHours) || in_array($toTime, $availableHours)) {
            return $this->addError('Classroom Already Choosen!');
        }
    }
}
