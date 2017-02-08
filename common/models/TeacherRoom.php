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
            [['classroomId'], 'required'],
			[['classroomId'], 'validateClassroomAvailability'],
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

    public function validateClassroomAvailability($attribute)
    {
        $locationId = Yii::$app->session->get('location_id');
        $teacherAvailability = TeacherAvailability::findOne($this->teacherAvailabilityId);
        $teacherRooms        = TeacherRoom::find()
            ->location($locationId)
            ->day($teacherAvailability->day)
            ->where(['classroomId' => $this->classroomId])
            ->all();
        foreach ($teacherRooms as $teacherRoom) {
            $fromTime = (new \DateTime($teacherRoom->teacherAvailability->from_time))->format('h:i A');
            $toTime   = (new \DateTime($teacherRoom->teacherAvailability->to_time))->format('h:i A');
            $start    = new \DateTime($teacherAvailability->from_time);
            $end      = new \DateTime($teacherAvailability->to_time);
            $interval = new \DateInterval('PT15M');
            $hours    = new \DatePeriod($start, $interval, $end);
            $this->checkClassroomAvailability($hours, $fromTime, $toTime, $attribute);

            $fromTime = (new \DateTime($teacherAvailability->from_time))->format('h:i A');
            $toTime   = (new \DateTime($teacherAvailability->to_time))->format('h:i A');
            $start    = new \DateTime($teacherRoom->teacherAvailability->from_time);
            $end      = new \DateTime($teacherRoom->teacherAvailability->to_time);
            $hours    = new \DatePeriod($start, $interval, $end);
            $this->checkClassroomAvailability($hours, $fromTime, $toTime, $attribute);
        }
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
}
