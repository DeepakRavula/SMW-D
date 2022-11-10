<?php

namespace common\models;

/**
 * This is the model class for table "release_notes_read".
 *
 * @property string $id
 * @property string $release_note_id
 * @property string $user_id
 */
class ReleaseNotesRead extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'release_notes_read';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['release_note_id', 'user_id'], 'required'],
            [['release_note_id', 'user_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'release_note_id' => 'Release Note ID',
            'user_id' => 'User ID',
        ];
    }

    public function getReleaseNote()
    {
        return $this->hasOne(ReleaseNotes::className(), ['release_note_id' => 'id']);
    }
}
