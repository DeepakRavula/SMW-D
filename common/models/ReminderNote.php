<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "reminder_note".
 *
 * @property string $id
 * @property string $text
 */
class ReminderNote extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'reminder_note';
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
