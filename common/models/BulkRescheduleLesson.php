<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "bulk_reschedule_lesson".
 *
 * @property int $id
 * @property int $lessonId
 */
class BulkRescheduleLesson extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bulk_reschedule_lesson';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lessonId'], 'required'],
            [['lessonId'], 'integer'],
            [['createdByUserId', 'updatedByUserId', 'updatedOn', 'createdOn'], 'safe'],
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
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdOn',
                'updatedAtAttribute' => 'updatedOn',
                'value' => (new \DateTime())->format('Y-m-d H:i:s'),
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'createdByUserId',
                'updatedByAttribute' => 'updatedByUserId'
            ],
        ];
    }
}
