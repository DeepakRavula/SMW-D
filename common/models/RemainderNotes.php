<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "remainder_notes".
 *
 * @property string $id
 * @property string $user_id
 * @property string $text
 * @property string $date
 */
class RemainderNotes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'remainder_notes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'text'], 'required'],
            [['user_id'], 'integer'],
            [['text'], 'string'],
            [['date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'text' => 'Text',
            'date' => 'Date',
        ];
    }
}
