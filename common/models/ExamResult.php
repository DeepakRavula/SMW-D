<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "exam_result".
 *
 * @property string $id
 * @property string $studentId
 * @property string $date
 * @property integer $mark
 * @property integer $level
 * @property string $program
 * @property string $type
 * @property string $teacherId
 */
class ExamResult extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'exam_result';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mark', 'level', 'program', 'teacherId'], 'required'],
            [['studentId', 'mark', 'level', 'teacherId'], 'integer'],
            [['date'], 'safe'],
            [['program'], 'string', 'max' => 50],
            [['type'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'studentId' => 'Student ID',
            'date' => 'Exam Date',
            'mark' => 'Mark',
            'level' => 'Level',
            'program' => 'Program',
            'type' => 'Type',
            'teacherId' => 'Teacher ID',
        ];
    }

	public function getTeacher()
    {
        return $this->hasOne(User::className(), ['id' => 'teacherId']);
    }
}
