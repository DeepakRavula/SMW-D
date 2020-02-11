<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "course_schedule_old_teacher".
 *
 * @property int $id
 * @property int $teacherId
 * @property int $courseScheduleId
 * @property int $courseId
 * @property string $createdOn
 * @property int $createdByUserId
 */
class CourseScheduleOldTeacher extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'course_schedule_old_teacher';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdOn',
                'updatedAtAttribute' => false,
                'value' => (new \DateTime())->format('Y-m-d H:i:s'),
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'createdByUserId',
                'updatedByAttribute' => false
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['teacherId', 'courseScheduleId', 'courseId', 'createdByUserId', 'endDate'], 'required'],
            [['teacherId', 'courseScheduleId', 'courseId', 'createdByUserId'], 'integer'],
            [['createdOn'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'teacherId' => 'Teacher ID',
            'courseScheduleId' => 'Course Schedule ID',
            'courseId' => 'Course ID',
            'createdOn' => 'Created On',
            'createdByUserId' => 'Created By User ID',
        ];
    }
}
