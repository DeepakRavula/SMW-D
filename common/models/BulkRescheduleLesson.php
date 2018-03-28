<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "bulk_reschedule_lesson".
 *
 * @property int $id
 * @property int $lessonId
 */
class BulkRescheduleLesson extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bulk_reschedule_lesson';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lessonId'], 'required'],
            [['lessonId'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lessonId' => 'Lesson ID',
        ];
    }
}
