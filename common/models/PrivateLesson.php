<?php

namespace common\models;

/**
 * This is the model class for table "private_lesson".
 *
 * @property string $id
 * @property string $lessonId
 * @property string $expiryDate
 * @property int $isElgible
 */
class PrivateLesson extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'private_lesson';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lessonId'], 'required'],
            [['lessonId'], 'integer'],
            [['expiryDate'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lessonId' => 'Lesson ID',
            'expiryDate' => 'Expiry Date',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return \common\models\query\PrivateLessonQuery the active query used by this AR class
     */
    public static function find()
    {
        return new \common\models\query\PrivateLessonQuery(get_called_class());
    }

    public function isExpired()
    {
        return (new \DateTime($this->expiryDate))->format('Y-m-d H:i:s') <
                (new \DateTime())->format('Y-m-d H:i:s');
    }
}
