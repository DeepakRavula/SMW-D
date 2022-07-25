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
        return 'customer_email_notification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['emailNotificationTypeId'],  'required'],
            [['emailNotificationTypeId'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\CustomerEmailNotificationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\CustomerEmailNotificationQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'emailNotificationTypeId' => 'EmailNotificationTypeId',
        ];
    }
    public function getUserContact()
    {
        return $this->hasMany(UserContact::className(), ['userId' => 'userId']);
    }
    public function getUserEmail()
    {
        return $this->hasMany(UserEmail::className(), ['userId' => 'userId']);
    }

}
