<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "private_lesson".
 *
 * @property string $id
 * @property string $lessonId
 * @property string $expiryDate
 * @property integer $isElgible
 */
class PrivateLesson extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'private_lesson';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lessonId', 'isElgible'], 'required'],
            [['lessonId', 'isElgible'], 'integer'],
            [['expiryDate'], 'safe'],
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
            'expiryDate' => 'Expiry Date',
            'isElgible' => 'Is Elgible',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\PrivateLessonQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\PrivateLessonQuery(get_called_class());
    }
}
