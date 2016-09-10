<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "enrolment".
 *
 * @property string $id
 * @property string $courseId
 * @property string $studentId
 * @property integer $isDeleted
 */
class Enrolment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'enrolment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['courseId', 'studentId', 'isDeleted'], 'required'],
            [['courseId', 'studentId', 'isDeleted'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'courseId' => 'Course ID',
            'studentId' => 'Student ID',
            'isDeleted' => 'Is Deleted',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\EnrolmentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\EnrolmentQuery(get_called_class());
    }
	
	public function getCourse() {
		return $this->hasOne(Course::className(), ['id' => 'courseId']);
	}

	public function getProgram() {
		return $this->hasOne(Program::className(), ['id' => 'programId'])
			->viaTable('course',['id' => 'courseId']);
	}
}
