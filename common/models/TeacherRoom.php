<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "teacher_room".
 *
 * @property string $id
 * @property integer $day
 * @property integer $classroomId
 */
class TeacherRoom extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'teacher_room';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['classroomId'], 'integer'],
			[['day'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'day' => 'Day',
            'classroomId' => 'Classroom ID',
        ];
    }
}
