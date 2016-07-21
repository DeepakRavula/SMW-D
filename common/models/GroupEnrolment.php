<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "group_enrolment".
 *
 * @property string $id
 * @property integer $course_id
 * @property integer $student_id
 */
class GroupEnrolment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'group_enrolment';
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
}
