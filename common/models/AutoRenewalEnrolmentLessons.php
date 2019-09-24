<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use asinfotrack\yii2\audittrail\behaviors\AuditTrailBehavior;

/**
 * This is the model class for table "auto_renewal_enrolment_lessons".
 *
 * @property int $id
 * @property int $lessonId
 * @property string $createdOn
 * @property string $updatedOn
 * @property int $createdByUserId
 * @property int $updatedByUserId
 */
class AutoRenewalEnrolmentLessons extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    const CONSOLE_USER_ID  = 727;

    public static function tableName()
    {
        return 'auto_renewal_enrolment_lessons';
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
            'audittrail' => [
                'class' => AuditTrailBehavior::className(), 
                'consoleUserId' => self::CONSOLE_USER_ID, 
                'attributeOutput' => [
                    'last_checked' => 'datetime',
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lessonId'], 'required'],
            [['lessonId', 'createdByUserId', 'updatedByUserId'], 'integer'],
            [['createdOn', 'updatedOn','createdByUserId', 'updatedByUserId'], 'safe'],
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
            'createdOn' => 'Created On',
            'updatedOn' => 'Updated On',
            'createdByUserId' => 'Created By User ID',
            'updatedByUserId' => 'Updated By User ID',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\AutoRenewalEnrolmentLessonsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\AutoRenewalEnrolmentLessonsQuery(get_called_class());
    }
}
