<?php

namespace common\models;

use yii2tech\ar\softdelete\SoftDeleteBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "customer_email_notification".
 *
 * @property int $id
 * @property string $emailNotificationTypeId
 */

class CustomerEmailNotification extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    // public $notificationEmailType;

    public static function tableName()
    {
        return 'auto_email_status';
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
