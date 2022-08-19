<?php

namespace common\models;


use Yii;


/**
 * This is the model class for table "auto_email_status".
 *
 * @property int $id
 * @property string $notificationType
 */

class PrivateLessonEmailStatus extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    // public $notificationType;

    public static function tableName()
    {
        return 'private_lesson_email_status';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lessonId'],  'required'],
            [['lessonId','notificationType'], 'integer'],
            [['status'], 'safe'],
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
            'notificationType' => 'Notification Type',
            'status' => 'Status',
        ];
    }
    

}
