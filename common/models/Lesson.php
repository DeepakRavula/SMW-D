<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "lesson".
 *
 * @property integer $id
 * @property string $student_id
 * @property string $teacher_id
 * @property string $program_id
 * @property double $rate
 * @property string $quantity
 * @property string $commencement_date
 * @property integer $invoiced_id
 * @property integer $location_id
 */
class Lesson extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%lesson}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['student_id', 'program_id', 'location_id'], 'required'],
            [['student_id', 'program_id', 'invoiced_id', 'location_id'], 'integer'],
            [['quantity', 'commencement_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'student_id' => 'Student Name',
            'program_id' => 'Program Name',
            'quantity' => 'Quantity',
            'commencement_date' => 'Commencement Date',
            'invoiced_id' => 'Invoiced ID',
            'location_id' => 'Location ID',
        ];
    }
	
	public function getStudentName()
    {
        return $this->hasOne(Student::className(), ['id' => 'student_id']);
    }

	public function getProgramName()
    {
        return $this->hasOne(Program::className(), ['id' => 'program_id']);
    }
}
