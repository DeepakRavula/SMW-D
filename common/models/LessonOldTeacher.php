<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "lesson_old_teacher".
 *
 * @property int $id
 * @property int $teacherId
 * @property int $lessonId
 * @property int $courseId
 * @property int $enrolmentId
 * @property string $createdOn
 * @property int $createdByUserId
 */
class LessonOldTeacher extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lesson_old_teacher';
    }

    /**
     * @inheritdoc
     */
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

    public function rules()
    {
        return [
            [['teacherId', 'lessonId', 'courseId', 'enrolmentId', 'createdByUserId'], 'required'],
            [['teacherId', 'lessonId', 'courseId', 'enrolmentId', 'createdByUserId'], 'integer'],
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
            'lessonId' => 'Lesson ID',
            'courseId' => 'Course ID',
            'enrolmentId' => 'Enrolment ID',
            'createdOn' => 'Created On',
            'createdByUserId' => 'Created By User ID',
        ];
    }
}
