<?php

namespace common\models;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

use Yii;

/**
 * This is the model class for table "auto_renewal_payment_cycle".
 *
 * @property int $id
 * @property int $autoRenewalId
 * @property int $paymentCycleId
 * @property string $createdOn
 * @property int $createdByUserId
 */
class AutoRenewalPaymentCycle extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auto_renewal_payment_cycle';
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
            [['autoRenewalId', 'paymentCycleId'], 'required'],
            [['autoRenewalId', 'paymentCycleId', 'createdByUserId'], 'integer'],
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
            'paymentCycleId' => 'Payment Cycle ID',
            'createdOn' => 'Created On',
            'createdByUserId' => 'Created By User ID',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\AutoRenewalPaymentCycleQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\AutoRenewalPaymentCycleQuery(get_called_class());
    }
}
