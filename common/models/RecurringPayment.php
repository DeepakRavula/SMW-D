<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use asinfotrack\yii2\audittrail\behaviors\AuditTrailBehavior;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "recurring_payment".
 *
 * @property int $id
 * @property int $paymentId
 * @property string $createdOn
 * @property string $updatedOn
 * @property int $createdByUserId
 * @property int $updatedByUserId
 */
class RecurringPayment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */

    const CONSOLE_USER_ID  = 727;

    public static function tableName()
    {
        return 'recurring_payment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['paymentId', 'customerRecurringPaymentId'], 'required'],
            [['paymentId', 'createdByUserId', 'updatedByUserId'], 'integer'],
            [['createdOn', 'updatedOn', 'createdByUserId', 'updatedByUserId'], 'safe'],
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
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'paymentId' => 'Payment ID',
            'createdOn' => 'Created On',
            'updatedOn' => 'Updated On',
            'createdByUserId' => 'Created By User ID',
            'updatedByUserId' => 'Updated By User ID',
        ];
    }

    public static function find()
    {
        return new \common\models\query\RecurringPaymentQuery(get_called_class());
    }
}
