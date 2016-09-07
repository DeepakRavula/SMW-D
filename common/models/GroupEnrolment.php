<?php

namespace common\models;

use Yii;
use \yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "group_enrolment".
 *
 * @property string $id
 * @property integer $course_id
 * @property integer $student_id
 */
class GroupEnrolment extends \yii\db\ActiveRecord
{
	public $studentIds;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'group_enrolment';
    }

	public function behaviors()
    {
        return [
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'isDeleted' => true
                ],
            ],
        ];
    }
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['course_id'], 'required'],
            [['course_id', 'student_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'course_id' => 'Course Name',
            'student_id' => 'Student Name',
            'studentIds' => 'Enrolled Student Name',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\GroupEnrolmentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\GroupEnrolmentQuery(get_called_class());
    }

	public function getGroupCourse()
    {
        return $this->hasOne(GroupCourse::className(), ['id' => 'course_id']);
    }

	public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'student_id']);
    }

	public function getProgram()
    {
        return $this->hasOne(Program::className(), ['id' => 'program_id'])
			->viaTable('group_course',['id' => 'course_id']);
    }

	public function getTeacher()
    {
		return $this->hasOne(User::className(), ['id' => 'teacher_id'])
			->viaTable('group_course',['id' => 'course_id']);
	}
}
