<?php

namespace common\models;

/**
 * This is the model class for table "release_notes".
 *
 * @property string $id
 * @property string $notes
 * @property string $date
 * @property string $user_id
 */
class ReleaseNotes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'release_notes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date', 'user_id'], 'required'],
            [['date'], 'safe'],
            [['user_id'], 'integer'],
            [['notes'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'notes' => 'Notes',
            'date' => 'Date',
            'user_id' => 'User ID',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getReleaseNoteReads()
    {
        return $this->hasMany(ReleaseNotesRead::className(), ['release_note_id' => 'id']);
    }

    public static function latestNotes()
    {
        return $query = self::find()->orderBy(['id' => SORT_DESC])->one();
    }
}
