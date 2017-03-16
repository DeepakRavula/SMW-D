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
