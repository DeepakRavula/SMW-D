<?php

namespace common\models;

use yii2tech\ar\softdelete\SoftDeleteBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "notification_email_type".
 *
 * @property int $id
 * @property string $emailNotifyType
 */

class NotificationEmailType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $notificationEmailType;

    public static function tableName()
    {
        return 'notification_email_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['emailNotifyType',],  'string', 'max' => 255],
            ['notificationEmailType', 'each', 'rule' => [
                'exist', 'tagetClass' => NotificationEmailType::className(),  'targetAttribute' => 'id']
            ],
        ];
    }

    public static function find()
    {
        return new \common\models\query\NotificationEmailTypeQuery(get_called_class());
    }

    public function behaviors()
    {
        return [
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'isDeleted' => true,
                ],
                'replaceRegularDelete' => true,
            ],
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdOn',
                'updatedAtAttribute' => 'updatedOn',
                'value' => (new \DateTime())->format('Y-m-d H:i:s'),
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'createdByUserId',
                'updatedByAttribute' => 'updatedByUserId',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'emailNotifyType' => 'EmailNotifyType',
            'notificationEmailType ' => 'notificationEmailType',
        ];
    }
    public static function emailNotifyList()
    {
        $data =  static::find()
            ->select(['id', 'emailNotifyType'])
            ->orderBy('id')->asArray()->all();
        return ArrayHelper::map($data, 'id', 'emailNotifyType');
    }

}