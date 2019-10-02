<?php

namespace common\models;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

use Yii;

/**
 * This is the model class for table "auto_renewal_lessons".
 *
 * @property int $id
 * @property int $autoRenewalId
 * @property int $lessonId
 * @property string $createdOn
 * @property int $createdByUserId
 */
class AutoRenewalLessons extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auto_renewal_lessons';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdOn',
                'updatedAtAttribute' => false,
                'value' => (new \DateTime())->format('Y-m-d H:i:s'),
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'createdByUserId',
                'updatedByAttribute' => false
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['autoRenewalId', 'lessonId'], 'required'],
            [['autoRenewalId', 'lessonId', 'createdByUserId'], 'integer'],
            [['createdOn'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'autoRenewalId' => 'Auto Renewal ID',
            'lessonId' => 'Lesson ID',
            'createdOn' => 'Created On',
            'createdByUserId' => 'Created By User ID',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\AutoRenewalLessonsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\AutoRenewalLessonsQuery(get_called_class());
    }

    
    public function getPaymentCycleLesson()
    {
        return $this->hasOne(PaymentCycleLesson::className(), ['lessonId' => 'lessonId'])
            ->onCondition(['payment_cycle_lesson.isDeleted' => false]);
    }
}
