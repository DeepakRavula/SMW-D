<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "reminder_notes".
 *
 * @property string $id
 * @property string $user_id
 * @property string $text
 * @property string $date
 */
class ReminderNotes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'reminder_notes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['notes'], 'required'],
            [['notes'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'notes' => 'Notes',
        ];
    }
}
