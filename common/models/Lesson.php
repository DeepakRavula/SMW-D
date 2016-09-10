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
}
