<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "course_extra".
 *
 * @property int $id
 * @property int $courseId
 * @property int $extraCourseId
 */
class CourseExtra extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'course_extra';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['courseId', 'extraCourseId'], 'required'],
            [['courseId', 'extraCourseId'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'courseId' => 'Course ID',
            'extraCourseId' => 'Extra Course ID',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\CourseExtraQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\CourseExtraQuery(get_called_class());
    }
}
