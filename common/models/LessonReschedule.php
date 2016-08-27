<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "item_type".
 *
 * @property string $id
 * @property string $name
 */
class LessonReschedule extends \yii\db\ActiveRecord
{
	const TYPE_PRIVATE_LESSON = 1;
	const TYPE_GROUP_LESSON = 2;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lesson_reschedule';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lesson_id', 'lesson_reschedule_id', 'type'], 'required'],
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
            'lesson_reschedule_id' => 'Lesson Reschedule Id',
			'type' => 'Type'
        ];
    }
}
