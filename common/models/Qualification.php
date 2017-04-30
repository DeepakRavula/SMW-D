<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "qualification".
 *
 * @property string $id
 * @property string $teacher_id
 * @property string $program_id
 */
class Qualification extends \yii\db\ActiveRecord
{
	const TYPE_HOURLY = 1;
	const TYPE_FIXED = 2;
	
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%qualification}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['program_id', 'rate'], 'required'],
            [['teacher_id', 'program_id', 'type'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'teacher_id' => 'Teacher Name',
            'program_id' => 'Program Name ',
			'rate' => 'Rate ($)'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeacher()
    {
        return $this->hasOne(User::className(), ['id' => 'teacher_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgram()
    {
        return $this->hasOne(Program::className(), ['id' => 'program_id']);
    }
}
