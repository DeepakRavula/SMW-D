<?php

namespace common\models;

use Yii;
use common\models\GroupCourse;

/**
 * This is the model class for table "group_lesson".
 *
 * @property string $id
 * @property string $course_id
 * @property integer $teacher_id
 * @property string $date
 * @property integer $status
 */
class GroupLesson extends \yii\db\ActiveRecord
{
	const STATUS_SCHEDULED = 1;
	const STATUS_COMPLETED = 2;
	const STATUS_CANCELED = 3;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'group_lesson';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['course_id', 'teacher_id','from_time','to_time'], 'required'],
            [['course_id', 'teacher_id', 'status'], 'integer'],
            [['date','from_time','to_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'course_id' => 'Course ID',
            'teacher_id' => 'Teacher ID',
            'date' => 'Date',
            'status' => 'Status',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\GroupLessonQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\GroupLessonQuery(get_called_class());
    }

	public function getTeacher()
    {
        return $this->hasOne(User::className(), ['id' => 'teacher_id']);
    }

	public function getGroupCourse()
    {
        return $this->hasOne(GroupCourse::className(), ['id' => 'course_id']);
    }
}
