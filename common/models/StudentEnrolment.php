<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "student_enrolment".
 *
 * @property integer $id
 * @property integer $student_id
 * @property integer $qualification_id
 * @property string $commencement_date
 * @property string $renewal_date
 */
class StudentEnrolment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'student_enrolment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['student_id', 'qualification_id'], 'required'],
            [['student_id', 'qualification_id'], 'integer'],
            [['commencement_date', 'renewal_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'student_id' => 'Student ID',
            'qualification_id' => 'Qualification ID',
            'commencement_date' => 'Commencement Date',
            'renewal_date' => 'Renewal Date',
        ];
    }
}
