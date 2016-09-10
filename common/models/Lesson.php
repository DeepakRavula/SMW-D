<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "lesson".
 *
 * @property string $id
 * @property string $enrolmentId
 * @property string $teacherId
 * @property string $date
 * @property integer $status
 * @property integer $isDeleted
 */
class Lesson extends \yii\db\ActiveRecord
{

	const TYPE_PRIVATE_LESSON = 1;
	const TYPE_GROUP_LESSON = 2;
	const STATUS_DRAFTED = 1;
	const STATUS_SCHEDULED = 2;
	const STATUS_COMPLETED = 3;
	const STATUS_CANCELED = 4;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lesson';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enrolmentId', 'teacherId', 'status', 'isDeleted'], 'required'],
            [['enrolmentId', 'teacherId', 'status', 'isDeleted'], 'integer'],
            [['date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'enrolmentId' => 'Enrolment ID',
            'teacherId' => 'Teacher ID',
            'date' => 'Date',
            'status' => 'Status',
            'isDeleted' => 'Is Deleted',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\LessonQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\LessonQuery(get_called_class());
    }

	public function getCourse() {
		return $this->hasOne(Course::className(), ['id' => 'courseId'])
			->viaTable('enrolment',['id' => 'enrolmentId']);
	}
}
