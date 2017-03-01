<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "teacher_rate".
 *
 * @property string $id
 * @property string $teacherId
 * @property double $hourlyRate
 */
class TeacherRate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'teacher_rate';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['teacherId', 'hourlyRate'], 'required'],
            [['teacherId'], 'integer'],
            [['hourlyRate'], 'number'],
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
            'hourlyRate' => 'Hourly Rate',
        ];
    }
}
