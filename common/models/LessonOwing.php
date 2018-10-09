<?php

namespace common\models;


/**
 * This is the model class for table "blog".
 *
 * @property string $lessonId
 * 
 */
class LessonOwing extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lesson_owing';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lessonId'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'LessonId',
        ];
    }

    public static function find()
    {
        return new \common\models\query\LessonOwingQuery(get_called_class());
    }
}
