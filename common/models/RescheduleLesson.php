<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "item_type".
 *
 * @property string $id
 * @property string $name
 */
class RescheduleLesson extends \yii\db\ActiveRecord
{
	const TYPE_PRIVATE_LESSON = 1;
	const TYPE_GROUP_LESSON = 2;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'reschedule_lesson';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lesson_id', 'reschedule_lesson_id', 'type'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lesson_id' => 'Lesson Id',
            'reschedule_lesson_id' => 'Reschedule Lesson Id',
			'type' => 'Type'
        ];
    }
}
